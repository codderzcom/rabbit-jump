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

    protected function generateMessage(array $params): AMQPMessage
    {
        return new AMQPMessage($this->generateMessageBody($params), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    }
}