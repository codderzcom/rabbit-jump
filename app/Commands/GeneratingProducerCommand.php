<?php

namespace RabbitJump\Commands;

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

        $channel->queue_declare(
            $this->queue['name'],
            $this->queue['passive'],
            $this->queue['durable'],
            $this->queue['exclusive'],
            $this->queue['auto_delete']
        );


        $delay = $params['delay'] ?? 1;

        $useRandomDelayPayload = $params['rd'] ?? false;

        while (\TRUE) {
            $message = ($params['m'] ?? 'Hello World!') . ' at ' . (new \DateTime())->format('H:i:s.u');

            if($useRandomDelayPayload) {
                $message .= ' d:' . \rand(1, 10);
            }

            $msg = new AMQPMessage($message );

            $channel->basic_publish($msg, '', 'hello');

            $this->content = " [✔] Sent '$message'\n";

            $this->render();

            sleep((int)$delay);
        }

        $this->freeChannel($channel);
    }

}