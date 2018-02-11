<?php

namespace RabbitJump\Controllers;

class HomeController extends BaseController
{
    protected $content = 'Hello world';

    public function index(): void
    {
        $this->render();
    }
}