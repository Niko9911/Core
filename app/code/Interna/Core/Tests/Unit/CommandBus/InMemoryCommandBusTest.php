<?php

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
            new \DateTimeImmutable(strftime(self::DATE))
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
            new \DateTimeImmutable(strftime(self::DATE))
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
            new \DateTimeImmutable(strftime(self::DATE))
        ));
    }
}