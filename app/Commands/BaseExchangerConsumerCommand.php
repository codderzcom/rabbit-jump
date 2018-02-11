<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;

abstract class BaseExchangerConsumerCommand extends WaitingConsumerCommand
{

    protected $exchangerName = 'basic';
    protected $queueName;

    protected function connectToQueue(AMQPChannel $channel): void
    {
        list($this->queueName, ,) = $channel->queue_declare("");
        $channel->queue_bind($this->queueName, $this->exchangerName);
    }

    protected function consumeMessage(AMQPChannel $channel, \Closure $callback): void
    {
        $channel->basic_consume($this->queueName, '', false, true, false, false, $callback);
    }
}