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
if (!(\PHP_VERSION_ID >= 70200)) {
    echo 'PHP 7.2 or greater is required!';
    throw new RuntimeException('PHP 7.2 or greater is required!');
}

if (!\extension_loaded('phalcon')
    || !\version_compare(\Phalcon\Version::get(), '3.4.0', '>=')) {
    echo 'Phalcon is required extension. (At least version 3.4.0).';
    throw new RuntimeException('Phalcon is required extension. (At least version 3.4.0).');
}

final class index extends \Phalcon\Mvc\User\Component
{
    private const CACHE = false; // Caches only XML.
    private const DEBUG = true;  // Error reporting.

    /**
     * SPECIAL:     9 [NO USE]
     * CUSTOM:      8 [NO USE]
     * DEBUG:       7 <- Development
     * INFO:        6
     * NOTICE:      5
     * WARNING:     4
     * ERROR:       3 <- Default
     * ALERT:       2
     * CRITICAL:    1
     * EMERGENCY:   0.
     */
    private const LOG_LEVEL = 7;


    public function __construct()
    {
        require dirname(__DIR__).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Interna.php';
        new Interna(self::CACHE, self::DEBUG, self::LOG_LEVEL);
    }
}

new index();
