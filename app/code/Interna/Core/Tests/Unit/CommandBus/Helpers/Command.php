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

final class Command
{
    /** @var int */
    private $int;

    /** @var string */
    private $string;

    /** @var \DateTimeImmutable */
    private $dateTime;

    public function __construct(
        int $int,
                                string $string,
                                \DateTimeImmutable $dateTimeImmutable
    ) {
        $this->int = $int;
        $this->string = $string;
        $this->dateTime = $dateTimeImmutable;
    }

    /**
     * @return int
     */
    public function getInt(): int
    {
        return $this->int;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }
}
