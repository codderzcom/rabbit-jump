<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class DirectProducerCommand extends BaseExchangerProducerCommand
{
    protected $exchanger = [
        'name' => 'directed',
        'type' => 'direct',
        'passive' => false,
        'durable' => false,
        'auto_delete' => false,
    ];

    protected $params = [];

    public function run(array $params): void
    {
        $this->params = $params;

        $channel = $this->getChannel();

        $this->connectToExchanger($channel);

        $msg = $this->generateMessage($params);

        $this->publishMessage($channel, $msg);

        $this->content = " [✔] Sent '" . $msg->getBody() . "'\n";

        $this->render();

        $this->freeChannel($channel);
    }

    protected function publishMessage(AMQPChannel $channel, AMQPMessage $msg): void
    {
        $this->connectToExchanger($channel);
        $channel->basic_publish($msg, $this->exchanger['name'],  $this->getRoutingKey());
    }

    protected function getRoutingKey(): string
    {
        return $this->params['rk'] ?? 'default';
    }
}