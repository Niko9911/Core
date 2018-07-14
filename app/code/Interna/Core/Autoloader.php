<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
 *
 * @copyright 2017-2018 (c) Niko Granö (https://granö.fi)
 * @copyright 2017-2018 (c) IronLions (https://ironlions.fi)
 */

namespace Interna\Core;

use Phalcon\Loader;
use Phalcon\Mvc\User\Component;

final class Autoloader extends Component
{
    public static function autoload(Config $entity): void
    {
        $loader = new Loader();
        $loader->registerNamespaces($entity->exportAutoload()['namespace']);
        $loader->register();
    }
}
