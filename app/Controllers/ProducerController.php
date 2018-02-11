<?php

namespace RabbitJump\Controllers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ProducerController extends BaseController
{
    public function send(string $message = null): void
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);

        $message = $message ?? 'Hello World!';
        $msg = new AMQPMessage( $message);
        $channel->basic_publish($msg, '', 'hello');

        $this->content = " [x] Sent '$message'\n";

        $this->render();

        $channel->close();
        $connection->close();
    }
}