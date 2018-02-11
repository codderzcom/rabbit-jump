<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class GeneratingProducerCommand extends BaseRJCommand
{

    protected $queue = [
        'name' => 'hello',
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => false,
    ];

    public function run(array $params): void
    {
        $channel = $this->getChannel();

        $this->connectToQueue($channel);

        $delay = $params['delay'] ?? 1;

        while (\TRUE) {
            $msg = $this->generateMessage($params);

            $this->publishMessage($channel, $msg);

            $this->content = " [✔] Sent '" . $msg->getBody() . "'\n";

            $this->render();

            sleep((int)$delay);
        }

        $this->freeChannel($channel);
    }


    protected function connectToQueue(AMQPChannel $channel): void
    {
        $channel->queue_declare(
            $this->queue['name'],
            $this->queue['passive'],
            $this->queue['durable'],
            $this->queue['exclusive'],
            $this->queue['auto_delete']
        );
    }

    protected function generateMessageBody(array $params): string
    {
        $useRandomDelayPayload = $params['rd'] ?? false;

        $message = ($params['m'] ?? 'Hello World!') . ' at ' . (new \DateTime())->format('H:i:s.u');

        if ($useRandomDelayPayload) {
            $message .= ' d:' . \rand(1, 10);
        }

        return $message;
    }

    protected function generateMessage(array $params): AMQPMessage
    {
        return new AMQPMessage($this->generateMessageBody($params));
    }

    protected function publishMessage(AMQPChannel $channel, AMQPMessage $msg): void
    {
        $channel->basic_publish($msg, '', $this->queue['name']);
    }

}