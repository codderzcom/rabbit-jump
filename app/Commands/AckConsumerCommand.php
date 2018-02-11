<?php

namespace RabbitJump\Commands;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class AckConsumerCommand extends WaitingConsumerCommand
{

    protected $queue = [
        'name' => 'hello',
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => false,
    ];

    protected function consumeMessage(AMQPChannel $channel, \Closure $callback): void
    {
        $channel->basic_consume('hello', '', false, false, false, false, $callback);
    }

    protected function done(AMQPMessage $msg): void
    {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        $time = (new \DateTime())->format('H:i:s.u');
        $this->content = " [✔] Done and acknowleged at $time. Waiting.\n" ;
        $this->render();
    }

}