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

namespace Interna\Core;

use Phalcon\Mvc\User\Component;

final class Checks extends Component
{
    public static function isWritableArray(array $paths): void
    {
        foreach ($paths as $path) {
            if (!\is_writable($path)) {
                throw new \RuntimeException("Path `$path` is not writable. Write access is required!");
            }
        }
    }
}
