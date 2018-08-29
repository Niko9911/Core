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

namespace Example\Welcome\Routes;

use Interna\Core\Mvc\AbstractRouter;

final class ExampleRouter extends AbstractRouter
{
    public function initialize(): void
    {
        $this->add('/', 'index::index');
    }

    /**
     * @return string Underscored path to module. Example: Example_Welcome_ExampleModule
     */
    protected function setModule(): string
    {
        return 'ExampleModule';
    }
}
