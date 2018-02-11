<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;

class FairConsumerCommand extends AckConsumerCommand
{

    protected function consumeMessage(AMQPChannel $channel, \Closure $callback): void
    {

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($this->queue['name'], '', false, false, false, false, $callback);
    }

}