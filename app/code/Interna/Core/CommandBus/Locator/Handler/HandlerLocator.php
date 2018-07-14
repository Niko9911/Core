<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
 *
 * @copyright 2017-2018 (c) Niko Granö (https://granö.fi)
 * @copyright 2017-2018 (c) IronLions (https://ironlions.fi)
 */

namespace Interna\Core\CommandBus\Locator\Handler;

use System\CommandBus\Exception\CommandHandlerNotExistException;

interface HandlerLocator
{
    /**
     * @param string $commandName
     *
     * @throws CommandHandlerNotExistException
     */
    public function getHandlerForCommand(string $commandName);
}
