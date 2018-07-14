<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
 *
 * @copyright 2017-2018 (c) Niko Granö (https://granö.fi)
 * @copyright 2017-2018 (c) IronLions (https://ironlions.fi)
 */

namespace Interna\Core\CommandBus\Locator\Listener;

use System\CommandBus\Exception\CommandHandlerNotExistException;

interface ListenersLocator
{
    /**
     * @param string $eventName
     *
     * @return \object[]
     *
     * @throws CommandHandlerNotExistException
     */
    public function getListenersForEvent(string $eventName): array;
}
