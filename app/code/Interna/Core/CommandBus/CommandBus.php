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

namespace Interna\Core\CommandBus;

use Interna\Core\CommandBus\Exception\HandlerMustHaveHandleMethodException;
use Interna\Core\CommandBus\Locator\Handler\HandlerLocator;

final class CommandBus implements CommandBusInterface
{
    /** @var HandlerLocator */
    private $handlerLocator;

    /**
     * CommandBus constructor.
     *
     * @param HandlerLocator $handlerLocator
     */
    public function __construct(HandlerLocator $handlerLocator)
    {
        $this->handlerLocator = $handlerLocator;
    }

    /**
     * @param $command
     *
     * @throws \Exception
     */
    public function handle($command): void
    {
        $handler = $this->handlerLocator->getHandlerForCommand(\get_class($command));

        if (!\method_exists($handler, 'handle')) {
            throw HandlerMustHaveHandleMethodException::byClassName(\get_class($handler));
        }

        $handler->handle($command);
    }
}
