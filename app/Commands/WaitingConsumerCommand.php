<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class WaitingConsumerCommand extends BaseRJCommand
{

    protected $queue = [
        'name' => 'hello',
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => false,
    ];

    public function run(array $params): void
    {
        $channel = $this->getChannel();

        $this->connectToQueue($channel);

        $this->content = ' [*] Waiting for messages. To exit press CTRL+C' . "\n";
        $this->render();

        $delay = (int)($params['delay'] ?? 0);

        $consumer = $this->createConsumer($delay);

        $this->consumeMessage($channel, $consumer);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    protected function connectToQueue(AMQPChannel $channel): void
    {
        $channel->queue_declare(
            $this->queue['name'],
            $this->queue['passive'],
            $this->queue['durable'],
            $this->queue['exclusive'],
            $this->queue['auto_delete']
        );
    }

    protected function createConsumer(int $delay): \Closure
    {
        return function ($msg) use ($delay) {
            $this->receive($msg);
            $this->delay($msg, $delay);
            $this->done($msg);
        };
    }

    protected function consumeMessage(AMQPChannel $channel, \Closure $callback): void
    {
        $channel->basic_consume($this->queue['name'], '', false, true, false, false, $callback);
    }


    protected function getDelayFromMessage(string $message): int
    {
        $reg = '/.*?\s(d:[0-9]+)$/';
        $matches = [];
        \preg_match($reg, $message, $matches);
        if (2 !== count($matches)) {
            return 0;
        }

        return (int)\explode(':', $matches[1])[1];
    }

    protected function receive(AMQPMessage $msg): void
    {
        $message = $msg->getBody();
        $time = (new \DateTime())->format('H:i:s.u');
        $this->content = " [•] Received '" . $message . "' on $time.\n";
        $this->render();
    }

    protected function delay(AMQPMessage $msg, int $delay): void
    {
        $delay = $delay ?: $this->getDelayFromMessage($msg->getBody());
        if ($delay > 0) {
            sleep($delay);
        }
    }

    protected function done(AMQPMessage $msg): void
    {
        $time = (new \DateTime())->format('H:i:s.u');
        $this->content = " [✔] Done at $time. Waiting.\n";
        $this->render();
    }

}