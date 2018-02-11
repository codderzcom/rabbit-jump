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

        $useRandomDelayPayload = $params['rd'] ?? false;

        while (\TRUE) {
            $message = ($params['m'] ?? 'Hello World!') . ' at ' . (new \DateTime())->format('H:i:s.u');

            if ($useRandomDelayPayload) {
                $message .= ' d:' . \rand(1, 10);
            }

            $msg = $this->generateMessage($message);

            $channel->basic_publish($msg, '', $this->queue['name']);

            $this->content = " [✔] Sent '$message'\n";

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

    protected function generateMessage(string $message): AMQPMessage
    {
        return new AMQPMessage($message);
    }

}