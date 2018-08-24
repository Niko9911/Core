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

namespace Interna\Core\Tests\Unit\CommandBus;

use Interna\Core\CommandBus\CommandBus;
use Interna\Core\CommandBus\Exception\CommandHandlerNotExistException;
use Interna\Core\CommandBus\Exception\HandlerMustHaveHandleMethodException;
use Interna\Core\CommandBus\Locator\Handler\InMemoryLocator;
use Interna\Core\Tests\Unit\CommandBus\Helpers\Command;
use Interna\Core\Tests\Unit\CommandBus\Helpers\CommandHandler;
use Interna\Core\Tests\Unit\CommandBus\Helpers\CommandHandlerMissing;
use Interna\Core\UnitTestCase;

final class InMemoryCommandBusTest extends UnitTestCase
{
    public const INT = 1;
    public const STRING = 'abc';
    public const DATE = '01.01.2000';

    /**
     * @throws \Exception
     */
    public function testSuccessFlow(): void
    {
        $locator = new InMemoryLocator(
            [
                Command::class => new CommandHandler(),
            ]
        );

        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(\strftime(self::DATE))
        ));
    }

    /**
     * @throws \Exception
     */
    public function testCommandHandlerMissing(): void
    {
        $this->expectException(HandlerMustHaveHandleMethodException::class);
        $locator = new InMemoryLocator(
            [
                Command::class => new CommandHandlerMissing(),
            ]
        );

        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(\strftime(self::DATE))
        ));
    }

    /**
     * @throws \Exception
     */
    public function testCommandMissing(): void
    {
        $this->expectException(CommandHandlerNotExistException::class);
        $locator = new InMemoryLocator(
            [
                'MissingCommand' => new CommandHandlerMissing(),
            ]
        );

        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(\strftime(self::DATE))
        ));
    }
}
