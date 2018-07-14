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

final class ClassCannotBeConstructedException extends \Exception
{
    /**
     * @param string $className
     *
     * @return ClassCannotBeConstructedException
     */
    public static function byClassName(string $className): self
    {
        return new self(
            \sprintf(
                'Class %s has been not constructed successfully.',
                $className
            )
        );
    }
}
