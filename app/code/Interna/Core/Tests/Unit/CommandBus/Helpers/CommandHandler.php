<?php

namespace Interna\Core\Tests\Unit\CommandBus\Helpers;

use PHPUnit\Framework\TestCase;

final class CommandHandler extends TestCase
{
    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $this->assertEquals(1, $command->getInt());
        $this->assertEquals('abc', $command->getString());
        $this->assertEquals(
            new \DateTimeImmutable(strftime('01.01.2000')),
            $command->getDateTime());
    }
}