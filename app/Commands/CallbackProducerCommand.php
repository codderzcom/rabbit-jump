<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class CallbackProducerCommand extends BaseRJCommand
{

    protected $queue = [
        'name' => '',
        'passive' => false,
        'durable' => false,
        'exclusive' => true,
        'auto_delete' => false,
    ];

    protected $response;

    protected $correlationId;

    protected $callbackQueueName;

    protected $publishQueueName = 'callback';

    public function run(array $params): void
    {
        $this->response = false;

        $this->correlationId = uniqid('cpc.');

        $channel = $this->getChannel();

        $this->callbackQueueName = $this->connectToQueue($channel);

        $this->connectToResponseQueue($channel);

        $msg = $this->generateMessage($params);

        $this->publishMessage($channel, $msg);

        $this->content = " [✔] Sent '" . $msg->getBody() . '. With correlation Id \'' . $this->correlationId . "' Waiting reply.'\n";

        $this->render();

        while (!$this->response) {
            $channel->wait();
        }

        $this->content = " [✔] Reply received: '" . $this->response . ". '\n";
        $this->render();

        $this->freeChannel($channel);
    }


    protected function connectToQueue(AMQPChannel $channel)
    {
        list($queueName, ,) = $channel->queue_declare(
            $this->queue['name'],
            $this->queue['passive'],
            $this->queue['durable'],
            $this->queue['exclusive'],
            $this->queue['auto_delete']
        );

        return $queueName;
    }

    protected function connectToResponseQueue(AMQPChannel $channel): void
    {
        $channel->basic_consume(
            $this->callbackQueueName,
            '',
            false,
            false,
            false,
            false,
            function ($msg) {
                $this->onResponse($msg);
            });
    }

    protected function generateMessageBody(array $params): string
    {
        $delay = (int) ($params['d'] ?? 1);

        $message = ($params['m'] ?? 'Hello World!') . ' at ' . (new \DateTime())->format('H:i:s.u');

        $message .= ' d:' . $delay;

        return $message;
    }

    protected function generateMessage(array $params): AMQPMessage
    {
        return new AMQPMessage($this->generateMessageBody($params), [
            'correlation_id' => $this->correlationId,
            'reply_to' => $this->callbackQueueName
        ]);
    }

    protected function publishMessage(AMQPChannel $channel, AMQPMessage $msg): void
    {
        $channel->basic_publish($msg, '', $this->publishQueueName);
    }

    protected function onResponse(AMQPMessage $msg): void
    {
        if ($msg->get('correlation_id') == $this->correlationId) {
            $this->response = $msg->body;
        }
    }

}