<?php

namespace RabbitJump;

use RabbitJump\Commands\BaseRJCommand;
use RabbitJump\Infra\AppAwareInterface;

class App
{
    /** @var Config */
    protected $config;


    public function __construct(?Config $config)
    {
        $this->applyConfig($config);
    }

    public function run(): void
    {
        $uriParts = $this->getURIParts();
        $callee = $this->getControllerCallee($uriParts);
        $class = \reset($callee);
        $method = \next($callee);
        $params = \next($callee) ?: \NULL;
        /** @var AppAwareInterface $runner */
        $runner = new $class();
        $runner->setApp($this);
        \is_null($params) ? $runner->$method() : $runner->$method(...$params);
    }

    public function runCli(): void
    {
        $options = \getopt('c:p:');
        if (!array_key_exists('c', $options)) {
            throw new \Exception("Command required. Use -c option to set command");
        }
        $callee = $this->getCommandCallee($options['c']);
        $runnerOption = $options['p'] ?? [];
        $params = $this->getCommandParams(is_array($runnerOption) ? $runnerOption : [$runnerOption]);
        /** @var BaseRJCommand $runner */
        $runner = new $callee();
        $runner->setApp($this);
        $runner->run($params);
    }

    public function config(): Config
    {
        return $this->config;
    }



    protected final function getControllerCallee(array $path): array
    {
        $namespace = $this->getControllerNamespace();
        $candidates = [];
        $homeController = $namespace . $this->config->homeController;
        $indexAction = $this->config->indexAction;
        if ($path) {
            $params = $path;
            $refinedUriPart = $this->toCamelCase(\array_shift($params));
            $candidates[] = $params ?
                [$this->toControllerName($namespace, $refinedUriPart), $this->toCamelCase(\array_shift($params)), $params] :
                [$this->toControllerName($namespace, $refinedUriPart), $indexAction];
        } else {
            $candidates[] = [$homeController, $indexAction];
        }
        return \array_reverse($candidates)[0];
    }

    protected final function getCommandCallee(string $command): string
    {
        $namespace = $this->getCommandNamespace();
        return $this->toCommandName($namespace, $this->toCamelCase($command, '_'));
    }

    public function getCommandParams(array $options): array
    {
        $params = [];
        foreach ($options as $option) {
            $param = explode('=', $option);
            if(2 !== count($param)) {
                continue;
            }
            if(!\array_key_exists($param[0], $params)) {
                $params[$param[0]] = $param[1];
            } else {
                if(\is_array($params[$param[0]])) {
                    $params[$param[0]][] = $param[1];
                } else {
                    $params[$param[0]] = [$params[$param[0]], $param[1]];
                }
            }
        }
        return $params;
    }


    protected function toCamelCase(string $uriPart, string $caseSymbol = '-'): string
    {
        return \str_replace(' ', '', \ucwords(\str_replace($caseSymbol, ' ', strtolower($uriPart))));
    }

    protected function getControllerNamespace(): string
    {
        return __NAMESPACE__ . '\\' . $this->config->controllersNamespace . '\\';
    }

    protected function getCommandNamespace(): string
    {
        return __NAMESPACE__ . '\\' . $this->config->commandsNamespace . '\\';
    }

    protected function toControllerName(string $namespace, string $subject): string
    {
        return $namespace . $subject . 'Controller';
    }

    protected function toCommandName(string $namespace, string $subject): string
    {
        return $namespace . $subject . 'Command';
    }

    protected function getURIParts(): array
    {
        $path = \trim(\filter_input(INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_STRING), ' /');
        return $path ? \explode('/', $path) : [];
    }

    protected function applyConfig(?Config $config): void
    {
        $this->config = $config ?? new Config();
    }

}
