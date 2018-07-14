<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
 *
 * @copyright 2017-2018 (c) Niko Granö (https://granö.fi)
 * @copyright 2017-2018 (c) IronLions (https://ironlions.fi)
 */

namespace Interna\Core\CommandBus;

use Core\Interna\CommandBus\Exception\HandlerMustHaveHandleMethodException;
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
