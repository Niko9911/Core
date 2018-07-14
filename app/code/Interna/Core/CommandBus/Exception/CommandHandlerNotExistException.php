<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
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