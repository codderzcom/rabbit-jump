<?php

namespace RabbitJump\Commands;

class FanoutProducerCommand extends BaseExchangerProducerCommand
{
    protected $exchanger = [
        'name' => 'basic',
        'type' => 'fanout',
        'passive' => false,
        'durable' => false,
        'auto_delete' => false,
    ];

}