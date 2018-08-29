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

interface CoreEventListenerInterface
{
    /**
     * This will be fired when application runs registerDefines on Interna class.
     */
    public function registerDefines(): void;

    /**
     * This will be fired when application runs registerModules on Interna class.
     */
    public function registerModules(): void;

    /**
     * This will be fired when application runs registerRoutes on Interna class.
     */
    public function registerRoutes(): void;

    /**
     * This will be fired when application runs registerCommandBus on Interna class.
     */
    public function registerCommandBus(): void;

    /**
     * This will be fired when application runs cacheConfigs on Interna class.
     */
    public function cacheConfigs(): void;

    /**
     * This will be fired when application runs run on Interna class.
     */
    public function run(): void;

    /**
     * This will be fired when application runs handleException on Interna class.
     */
    public function handleException(): void;

    /**
     * This will be fired when application has been succeed everything.
     * This will not run in case of exception.
     */
    public function done(): void;
}
