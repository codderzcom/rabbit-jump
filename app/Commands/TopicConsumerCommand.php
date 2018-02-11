<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;

class TopicConsumerCommand extends DirectConsumerCommand
{
    protected $exchanger = [
        'name' => 'topican',
        'type' => 'topic',
        'passive' => false,
        'durable' => false,
        'auto_delete' => false,
    ];
}