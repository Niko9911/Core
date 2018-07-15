<?php

namespace Interna\Core\Tests\Unit\CommandBus\Helpers;


final class Command
{
    /** @var int */
    private $int;

    /** @var string */
    private $string;

    /** @var \DateTimeImmutable */
    private $dateTime;

    public function __construct(int $int,
                                string $string,
                                \DateTimeImmutable $dateTimeImmutable
    )
    {
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