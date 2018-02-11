<?php

namespace RabbitJump\Controllers;

use RabbitJump\Infra\AppAwareInterface;
use RabbitJump\Infra\AppAwareTrait;

abstract class BaseController implements AppAwareInterface
{
    use AppAwareTrait;

    protected $content = '';

    public function render(): void
    {
        echo \json_encode($this->content);
    }
}