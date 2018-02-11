<?php

namespace RabbitJump\Infra;

use RabbitJump\App;

interface AppAwareInterface
{
    public function getApp(): App;

    public function setApp(App $app): void;
}