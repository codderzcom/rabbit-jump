<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Message\AMQPMessage;

class ProducerCommand extends BaseRJCommand
{

    public function run(array $params): void
    {
        $channel = $this->getChannel();

        $channel->queue_declare('hello', false, false, false, false);

        $message = $params['m'] ?? 'Hello World!';

        $msg = new AMQPMessage($message);

        $channel->basic_publish($msg, '', 'hello');

        $this->content = " [✔] Sent '$message'\n";

        $this->render();

        $this->freeChannel($channel);
    }
}