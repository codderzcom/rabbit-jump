<?php

namespace RabbitJump\Commands;

class DurableProducerCommand extends GeneratingProducerCommand
{

    protected $queue = [
        'name' => 'hello',
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'auto_delete' => false,
    ];
}