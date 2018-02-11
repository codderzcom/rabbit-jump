<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Message\AMQPMessage;

class DurableProducerCommand extends GeneratingProducerCommand
{

    protected $queue = [
        'name' => 'durable_hello',
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'auto_delete' => false,
    ];

    protected function generateMessage(string $message): AMQPMessage
    {
        return new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    }
}