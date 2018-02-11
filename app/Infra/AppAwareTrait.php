<?php

namespace RabbitJump\Infra;

use RabbitJump\App;
use RabbitJump\Config;

trait AppAwareTrait
{
    /** @var App */
    private $app;

    public function getApp(): App
    {
        return $this->app;
    }

    public function setApp(App $app): void
    {
        $this->app = $app;
    }

    public function appConfig(): Config
    {
        return $this->app->config();
    }
}