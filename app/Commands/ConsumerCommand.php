<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Message\AMQPMessage;

class ConsumerCommand extends BaseRJCommand
{
    public function run(array $params): void
    {
        $channel = $this->getChannel();

        $channel->queue_declare('hello', false, false, false, false);

        $this->content = " [*] Consuming one pending message. \n";
        $this->render();

        /** @var AMQPMessage $msg */
        $msg = $channel->basic_get('hello');
        if ($msg) {
            echo " [✔] Received '" . $msg->body . "'\n";
            $channel->basic_ack($msg->delivery_info['delivery_tag']);
        } else {
            echo " [✖] No pending messages. \n";
        }
        $this->freeChannel($channel);
    }
}