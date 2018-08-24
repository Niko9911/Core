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

use PHPUnit\Framework\Assert;

final class CommandHandlerWithDep
{
    private $dependency;

    public function __construct(
        Dependency $dependency,
        object $dep_object,
        int $dep_int,
        float $dep_float,
        string $dep_string,
        array $dep_array
    ) {
        Assert::assertEquals((object)['test'=>123], $dep_object);
        Assert::assertEquals(123, $dep_int);
        Assert::assertEquals(123.45, $dep_float);
        Assert::assertEquals('abc', $dep_string);

        $this->dependency = $dependency;
    }

    /**
     * @param Command $command
     *
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        Assert::assertEquals(Dependency::STRING, $this->dependency->getString());
        Assert::assertEquals(1, $command->getInt());
        Assert::assertEquals('abc', $command->getString());
        Assert::assertEquals(
            new \DateTimeImmutable(\strftime('01.01.2000')),
            $command->getDateTime()
        );
    }
}
