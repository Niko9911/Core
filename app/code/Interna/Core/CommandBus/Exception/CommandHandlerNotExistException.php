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

final class CommandHandlerNotExistException extends \Exception
{
    /**
     * @param string $commandClassName
     *
     * @return CommandHandlerNotExistException
     */
    public static function byClassName(string $commandClassName): self
    {
        return new self(\sprintf('Handler for command %s is not configured.', $commandClassName));
    }
}
