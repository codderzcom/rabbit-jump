<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;

class DirectConsumerCommand extends BaseExchangerConsumerCommand
{
    protected $exchanger = [
        'name' => 'directed',
        'type' => 'direct',
        'passive' => false,
        'durable' => false,
        'auto_delete' => false,
    ];
    protected $queueName;
    protected $params = [];

    public function run(array $params): void
    {
        $this->params = $params;
        parent::run($params);
    }

    protected function connectToQueue(AMQPChannel $channel): void
    {
        $channel->exchange_declare(
            $this->exchanger['name'],
            $this->exchanger['type'],
            $this->exchanger['passive'],
            $this->exchanger['durable'],
            $this->exchanger['auto_delete']
        );
        list($this->queueName, ,) = $channel->queue_declare("");
        foreach ($this->getRoutingKeys() as $key) {
            $channel->queue_bind($this->queueName, $this->exchanger['name'], $key);
        }
    }

    protected function getRoutingKeys(): array
    {
        if(\array_key_exists('rk', $this->params)) {
            return \is_array($this->params['rk']) ? $this->params['rk'] : [$this->params['rk']];
        }
        return ['default'];
    }

}