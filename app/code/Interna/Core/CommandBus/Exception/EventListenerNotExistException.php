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

final class EventListenerNotExistException extends \Exception
{
    /**
     * @param string $eventClassName
     *
     * @return EventListenerNotExistException
     */
    public static function byClassName(string $eventClassName): self
    {
        return new self(\sprintf('Listener for event %s is not configured.', $eventClassName));
    }
}
