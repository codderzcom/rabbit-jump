<?php

namespace RabbitJump\Commands;

class DurableConsumerCommand extends WaitingConsumerCommand
{

    protected $queue = [
        'name' => 'hello',
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'auto_delete' => false,
    ];

}