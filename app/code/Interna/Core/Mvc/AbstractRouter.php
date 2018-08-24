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

namespace Interna\Core\Mvc;

use Phalcon\Mvc\Router\Group;

abstract class AbstractRouter extends Group
{
    public function __construct($paths = null)
    {
        $this->setPaths(
            [
                'module'    => $this->setModule(),
                'namespace' => $this->setNamespace(),
            ]
        );
        parent::__construct($paths);
    }

    /**
     * @return string Underscored path to module. Example: Example_Welcome_ExampleModule
     */
    abstract protected function setModule(): string;

    /**
     * @return string Namespace to controllers. Example: Example\Welcome\Controllers
     */
    abstract protected function setNamespace(): string;

    abstract public function initialize(): void;
}
