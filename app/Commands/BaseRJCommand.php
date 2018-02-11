<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use RabbitJump\Infra\AppAwareInterface;
use RabbitJump\Infra\AppAwareTrait;

abstract class BaseRJCommand implements AppAwareInterface
{
    use AppAwareTrait;

    /** @var AMQPStreamConnection */
    private $connection;

    private $channels = [];

    protected $content = '';

    abstract public function run(array $params): void;

    protected function render(): void
    {
        echo $this->content;
    }

    protected function getChannel(int $id = null): AMQPChannel
    {
        if (!$this->connection) {
            $this->openConnection();
        }
        $channel = $this->connection->channel($id);

        $this->channels[$channel->getChannelId()] = $channel;

        return $channel;
    }

    protected function freeChannel(AMQPChannel $channel): void
    {
        unset($this->channels[$channel->getChannelId()]);
        $channel->close();
        if (0 === \count($this->channels)) {
            $this->closeConnection();
        }
    }

    protected function openConnection(): void
    {
        if ($this->connection && $this->connection->isConnected()) {
            $this->connection->reconnect();
        }

        $this->connection = new AMQPStreamConnection(
            $this->appConfig()->amqp_host,
            $this->appConfig()->amqp_port,
            $this->appConfig()->amqp_user,
            $this->appConfig()->amqp_pass
        );
    }

    protected function closeConnection(): void
    {
        if ($this->connection && $this->connection->isConnected()) {
            try {
                $this->connection->close();
            } catch (AMQPRuntimeException $exception) {
                //ignore
            }
        }
    }
}