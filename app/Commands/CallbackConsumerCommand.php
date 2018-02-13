<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class CallbackConsumerCommand extends WaitingConsumerCommand
{

    protected $queue = [
        'name' => 'callback',
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => false,
    ];

    protected function consumeMessage(AMQPChannel $channel, \Closure $callback): void
    {
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($this->queue['name'], '', false, false, false, false, $callback);
    }


    protected function done(AMQPMessage $msg): void
    {

        $reply = new AMQPMessage(
            'Reply for: ' . $msg->getBody() ,
            ['correlation_id' => $msg->get('correlation_id')]
        );

        $msg->delivery_info['channel']->basic_publish($reply, '', $msg->get('reply_to'));
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        $time = (new \DateTime())->format('H:i:s.u');
        $this->content = " [✔] Sent reply at $time.";
        $this->content .= ' Reply to: ' . $msg->get('reply_to'). '.';
        $this->content .= ' Delivery tag: ' . $msg->delivery_info['delivery_tag'];
        $this->content .= ' Correlation id: ' . $msg->get('correlation_id');
        $this->content .= " Waiting.\n";
        $this->render();
    }

}