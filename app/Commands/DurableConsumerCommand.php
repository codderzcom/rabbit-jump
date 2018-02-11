<?php

namespace RabbitJump\Commands;

class DurableConsumerCommand extends WaitingConsumerCommand
{

    protected $queue = [
        'name' => 'durable_hello',
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'auto_delete' => false,
    ];

}