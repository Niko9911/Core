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

namespace Interna\Core\CommandBus\Locator\Listener;

use Interna\Core\CommandBus\Exception\EventListenerNotExistException;

final class InMemoryLocator implements ListenersLocator
{
    /** @var \object[][] */
    private $listeners;

    /**
     * InMemoryLocator constructor.
     *
     * @param array $eventClassToListenersMap
     */
    public function __construct(array $eventClassToListenersMap)
    {
        $this->addListeners($eventClassToListenersMap);
    }

    /**
     * Returns listeners bound to the event's class name.
     *
     * @param string $eventName
     *
     * @return \object[]
     *
     * @throws EventListenerNotExistException
     */
    public function getListenersForEvent(string $eventName): array
    {
        if (!isset($this->listeners[$eventName])) {
            throw EventListenerNotExistException::byClassName($eventName);
        }

        return $this->listeners[$eventName];
    }

    /**
     * Bind a listeners instance to receive all events with a certain class.
     *
     * @param \object $listeners Listeners to receive class
     * @param string  $eventName Event class e.g. "My\TaskAddedCommand"
     */
    private function addListener(string $eventName, $listeners): void
    {
        $this->listeners[$eventName] = $listeners;
    }

    /**
     * Allows you to add multiple listeners at once.
     *
     * The map should be an array in the format of:
     *  [
     *      AddTaskCommand::class      => $someHandlerInstance,
     *      CompleteTaskCommand::class => $someHandlerInstance,
     *  ]
     *
     * @param array $commandClassToHandlerMap
     */
    private function addListeners(array $commandClassToHandlerMap): void
    {
        foreach ($commandClassToHandlerMap as $eventName => $listeners) {
            $this->addListener($eventName, $listeners);
        }
    }
}
