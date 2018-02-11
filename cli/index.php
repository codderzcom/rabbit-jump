<?php

require __DIR__.'/../vendor/autoload.php';

$app = new RabbitJump\App(new \RabbitJump\Config());

$app->runCli();