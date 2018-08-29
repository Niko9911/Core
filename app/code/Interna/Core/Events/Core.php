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

namespace Interna\Core\Events;

use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;

final class Core implements EventsAwareInterface
{
    private $eventsManager;

    /**
     * Sets the events manager.
     *
     * @param ManagerInterface $eventsManager
     */
    public function setEventsManager(ManagerInterface $eventsManager): void
    {
        $this->eventsManager = $eventsManager;
    }

    /**
     * Returns the internal event manager.
     *
     * @return ManagerInterface
     */
    public function getEventsManager(): ManagerInterface
    {
        return $this->eventsManager;
    }

    /**
     * This will be fired when application runs registerDefines on Interna class.
     */
    public function registerDefines(): void
    {
        $this->getEventsManager()->fire('core:registerDefines', $this);
    }

    /**
     * This will be fired when application runs registerModules on Interna class.
     */
    public function registerModules(): void
    {
        $this->getEventsManager()->fire('core:registerModules', $this);
    }

    /**
     * This will be fired when application runs registerRoutes on Interna class.
     */
    public function registerRoutes(): void
    {
        $this->getEventsManager()->fire('core:registerRoutes', $this);
    }

    /**
     * This will be fired when application runs registerCommandBus on Interna class.
     */
    public function registerCommandBus(): void
    {
        $this->getEventsManager()->fire('core:registerCommandBus', $this);
    }

    /**
     * This will be fired when application runs cacheConfigs on Interna class.
     */
    public function cacheConfigs(): void
    {
        $this->getEventsManager()->fire('core:cacheConfigs', $this);
    }

    /**
     * This will be fired when application runs run on Interna class.
     */
    public function run(): void
    {
        $this->getEventsManager()->fire('core:run', $this);
    }

    /**
     * This will be fired when application runs handleException on Interna class.
     */
    public function handleException(): void
    {
        $this->getEventsManager()->fire('core:handleException', $this);
    }

    /**
     * This will be fired when application has been succeed everything.
     * This will not run in case of exception.
     */
    public function done(): void
    {
        $this->getEventsManager()->fire('core:done', $this);
    }
}
