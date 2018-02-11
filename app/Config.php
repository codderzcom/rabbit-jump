<?php

namespace RabbitJump;

/**
 * Class Config
 * @package RabbitJump
 * @property string $controllersNamespace;
 * @property string $commandsNamespace;
 * @property string $baseController;
 * @property string $homeController;
 * @property string $indexAction;
 */
class Config
{
    private const CONTROLLERS_LOCATION = "Controllers";
    private const COMMANDS_LOCATION = "Commands";
    private const BASE_CONTROLLER = 'BaseController';
    private const HOME_CONTROLLER = 'HomeController';
    private const INDEX_ACTION = 'index';

    protected $storage = [
        'commandsNamespace' => self::COMMANDS_LOCATION,
        'controllersNamespace' => self::CONTROLLERS_LOCATION,
        'baseController' => self::BASE_CONTROLLER,
        'homeController' => self::HOME_CONTROLLER,
        'indexAction' => self::INDEX_ACTION,

        'amqp_host' => 'localhost',
        'amqp_port' => '5672',
        'amqp_user' => 'guest',
        'amqp_pass' => 'guest',

    ];

    public function __get($name)
    {
        return @$this->storage[$name] ?? \NULL;
    }

    public function __set($name, $value)
    {
        @$this->storage[$name] = $value;
    }
}