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

namespace Example\Welcome\Modules;

use Interna\Core\Mvc\AbstractModule;

final class ExampleModule extends AbstractModule
{
    /**
     * Registers services related to the module.
     *
     * @param \Phalcon\DiInterface $dependencyInjector
     *
     * @throws \Interna\Core\Exception\NamespaceParseException
     */
    public function registerServices(\Phalcon\DiInterface $dependencyInjector): void
    {
        // Setup view Service.
        /* @noinspection PhpUndefinedConstantInspection */
        //$dependencyInjector->set('view', Volt::setup(EXAMPLE_WELCOME.DS.'Views'), true);
        $this->registerViewVolt($dependencyInjector);

        // Set dispatcher service use a default namespace.
        $this->registerDispatcher($dependencyInjector);
    }
}
