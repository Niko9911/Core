<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
 *
 * @copyright 2017-2018 (c) Niko Granö (https://granö.fi)
 * @copyright 2017-2018 (c) IronLions (https://ironlions.fi)
 */

namespace Interna\Core\CommandBus\Locator\Handler;

use Phalcon\Mvc\User\Component;
use Interna\Core\CommandBus\Exception\ClassCannotBeConstructedException;
use Interna\Core\CommandBus\Exception\CommandHandlerNotExistException;

final class ConfigLocator extends Component implements HandlerLocator
{
    /**
     * @var \object[]
     */
    private $handlers;

    /**
     * @var \object[]
     */
    private $builtHandlers = [];

    /** @var \object[] */
    private $dependencies;

    /**
     * InMemoryLocator constructor.
     *
     * @param \ArrayAccess $configuration
     */
    public function __construct(\ArrayAccess $configuration)
    {
        $this->addConfigHandlers($configuration);
    }

    /**
     * Returns the handler bound to the command's class name.
     *
     * @param string $commandName
     *
     * @throws ClassCannotBeConstructedException
     * @throws CommandHandlerNotExistException
     *
     * @return \object
     */
    public function getHandlerForCommand(string $commandName)
    {
        if (!isset($this->handlers[$commandName])) {
            throw CommandHandlerNotExistException::byClassName($commandName);
        }

        if (isset($this->dependencies[$commandName])) {
            if (!isset($this->builtHandlers[$commandName])) {
                $this->builtHandlers[$commandName] = $this->constructClass(
                    (string)$this->handlers[$commandName],
                    (array)$this->dependencies[$commandName]
                );
            }
        } else {
            return new $this->handlers[$commandName]();
        }

        return $this->builtHandlers[$commandName];
    }

    /**
     * Bind a handler instance to receive all commands with a certain class.
     *
     * @param string  $commandName Command class e.g. "My\TaskAddedCommand"
     * @param \object $handler     Handler to receive class
     */
    private function addHandler(string $commandName, $handler): void
    {
        $this->handlers[$commandName] = $handler;
    }

    /**
     * @param \ArrayAccess $config
     */
    private function addConfigHandlers(\ArrayAccess $config): void
    {
        foreach ($config as $command) {
            $key = (string)$command['@attributes']['command'];
            if (isset($command['@attributes']['handler'])) {
                $map[$key] = (string)$command['@attributes']['handler'];
            } else {
                $map[$key] = $key.'Handler';
            }

            if (isset($command->dependencies)) {
                $this->dependencies[$key] = $command->dependencies;
            }
        }
        $this->addHandlers($map ?? []);
    }

    /**
     * Allows you to add multiple handlers at once.
     *
     * The map should be an array in the format of:
     *  [
     *      AddTaskCommand::class      => $someHandlerInstance,
     *      CompleteTaskCommand::class => $someHandlerInstance,
     *  ]
     *
     * @param array $commandClassToHandlerMap
     */
    private function addHandlers(array $commandClassToHandlerMap): void
    {
        foreach ($commandClassToHandlerMap as $commandName => $handler) {
            $this->addHandler($commandName, $handler);
        }
    }

    /**
     * @param string $className
     * @param array  $dependencies
     *
     * @throws ClassCannotBeConstructedException
     *
     * @return \object
     */
    public function constructClass(string $className, array $dependencies = [])
    {
        try {
            $reflector = new \ReflectionClass($className);
            $constructedDependencies = $this->constructDependencies($dependencies);

            return $constructedDependencies
                ? $reflector->newInstanceArgs($constructedDependencies)
                : $reflector->newInstance();
        } catch (\Exception $e) {
            throw ClassCannotBeConstructedException::byClassName($className);
        }
    }

    /**
     * @param array $dependencies
     *
     * @return array
     */
    public function constructDependencies(array $dependencies): array
    {
        $constructedDependencies = [];
        foreach ($dependencies as $constructorParameterName => $dependencyConfiguration) {
            $constructedDependencies[$constructorParameterName] = $this->resolve(
                $dependencyConfiguration['type'] ?? 'class',
                $dependencyConfiguration['value']
                ?? $dependencyConfiguration['items']
                ?? $dependencyConfiguration
            );
        }

        return $constructedDependencies;
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return mixed
     */
    private function resolve(string $type, $value)
    {
        switch ($type) {
            case 'class':
                return new $value();
            case 'object':
                return (object)$value;
            case 'int':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'string':
                return $value;
            case 'array':
                return \array_map(function ($arrayElement) {
                    return $this->resolve(
                        $arrayElement['type'],
                        'array' === $arrayElement['type'] ? $arrayElement['items'] : $arrayElement['value']
                    );
                }, (array)$value);
            default:
                return null;
        }
    }
}
