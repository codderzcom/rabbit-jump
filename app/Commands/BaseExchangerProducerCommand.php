<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

abstract class BaseExchangerProducerCommand extends GeneratingProducerCommand
{
    protected $exchanger = [
        'name' => 'basic',
        'type' => 'fanout',
        'passive' => false,
        'durable' => false,
        'auto_delete' => false,
    ];

    protected function publishMessage(AMQPChannel $channel, AMQPMessage $msg): void
    {
        $this->connectToExchanger($channel);
        $channel->basic_publish($msg, $this->exchanger['name']);
    }

    protected function connectToExchanger(AMQPChannel $channel): void
    {
        $channel->exchange_declare(
            $this->exchanger['name'],
            $this->exchanger['type'],
            $this->exchanger['passive'],
            $this->exchanger['durable'],
            $this->exchanger['auto_delete']
        );
    }

}