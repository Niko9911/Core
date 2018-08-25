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

namespace Interna\Core;

if (!(\PHP_VERSION_ID >= 70200)) {
    echo 'PHP 7.2 or greater is required!';
    throw new \RuntimeException('PHP 7.2 or greater is required!');
}

if (!\extension_loaded('phalcon')
    || !\version_compare(\Phalcon\Version::get(), '3.4.0', '>=')) {
    echo 'Phalcon is required extension. (At least version 3.4.0).';
    throw new \RuntimeException('Phalcon is required extension. (At least version 3.4.0).');
}

abstract class UnitTestCase extends \Phalcon\Test\PHPUnit\UnitTestCase
{
    /**
     * UnitTestCase constructor.
     *
     * @param null|string $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        \chdir(\dirname(__DIR__, 3));
        require_once \dirname(__DIR__, 3).\DIRECTORY_SEPARATOR.'Interna.php';
        parent::__construct($name, $data, $dataName);
        $app = new \Interna(false, true, 9, true);
        $this->setUp();
    }
}
