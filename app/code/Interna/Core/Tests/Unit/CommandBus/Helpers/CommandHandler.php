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

namespace Interna\Core\Tests\Unit\CommandBus\Helpers;

use PHPUnit\Framework\TestCase;

final class CommandHandler extends TestCase
{
    /**
     * @param Command $command
     *
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $this->assertEquals(1, $command->getInt());
        $this->assertEquals('abc', $command->getString());
        $this->assertEquals(
            new \DateTimeImmutable(\strftime('01.01.2000')),
            $command->getDateTime()
        );
    }
}
