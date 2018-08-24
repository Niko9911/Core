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

namespace Interna\Core\CommandBus\Exception;

final class HandlerMustHaveHandleMethodException extends \Exception
{
    /**
     * @param string $commandHandlerClassName
     *
     * @return HandlerMustHaveHandleMethodException
     */
    public static function byClassName(string $commandHandlerClassName): self
    {
        return new self(
            \sprintf(
                'Class %s does not have "handle" method which is required for Command Handler.',
                $commandHandlerClassName
            )
        );
    }
}
