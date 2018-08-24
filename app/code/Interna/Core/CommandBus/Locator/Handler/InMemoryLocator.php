<?php

declare(strict_types=1);

/**
 * Interna Core — PHP Framework on Phalcon — NOTICE OF LICENSE
 * This source file is released under EUPL 1.2 license by copyright holders.
 * Please see LICENSE file for more specific information about terms.
 *
 * @copyright 2017-2018 (c) Niko Granö (https://granö.fi)
 * @copyright 2017-2018 (c) IronLions (https://ironlions.fi)
 */

namespace Interna\Core\CommandBus\Locator\Handler;

use Interna\Core\CommandBus\Exception\CommandHandlerNotExistException;

final class InMemoryLocator implements HandlerLocator
{
    /**
     * @var \object[]
     */
    private $handlers;

    /**
     * InMemoryLocator constructor.
     *
     * @param array $commandClassToHandlerMap
     */
    public function __construct(array $commandClassToHandlerMap)
    {
        $this->addHandlers($commandClassToHandlerMap);
    }

    /**
     * Returns the handler bound to the command's class name.
     *
     * @param string $commandName
     *
     * @return \object
     *
     * @throws CommandHandlerNotExistException
     */
    public function getHandlerForCommand(string $commandName)
    {
        if (!isset($this->handlers[$commandName])) {
            throw CommandHandlerNotExistException::byClassName($commandName);
        }

        return $this->handlers[$commandName];
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
}
