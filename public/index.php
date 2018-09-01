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
        chdir(dirname(__DIR__));
        require \dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
        new Interna(self::CACHE, self::DEBUG, self::LOG_LEVEL);
    }
}
new index();
