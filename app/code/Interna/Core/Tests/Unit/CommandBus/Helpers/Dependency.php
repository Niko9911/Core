<?php
/**
 * Created by PhpStorm.
 * User: niko
 * Date: 7/15/18
 * Time: 3:09 AM
 */

namespace Interna\Core\Tests\Unit\CommandBus\Helpers;


final class Dependency
{
    public const STRING = 'abc';

    public function getString(): string
    {
        return self::STRING;
    }
}