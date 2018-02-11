<?php

namespace RabbitJump\Commands;

class TopicProducerCommand extends DirectProducerCommand
{
    protected $exchanger = [
        'name' => 'topican',
        'type' => 'topic',
        'passive' => false,
        'durable' => false,
        'auto_delete' => false,
    ];
}