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

namespace Interna\Core\Services;

use Interna\Core\Exception\InvalidConfigurationException;
use Phalcon\Di;
use Phalcon\Session\Adapter;

final class Session
{
    public static function files(): Adapter
    {
        $session = new Adapter\Files(Di::getDefault()->get('config')->session->name);
        $session->start();

        return $session;
    }

    /**
     * @return Adapter
     *
     * @throws InvalidConfigurationException
     */
    public static function redis(): Adapter
    {
        try {
            $config = Di::getDefault()->get('config')->cache->connection;
        } catch (\Throwable $e) {
            throw new InvalidConfigurationException('Session connection is not configured.');
        }
        $session = new Adapter\Redis(
            [
                'uniqueId'   => Di::getDefault()->get('config')->session->name,
                'host'       => \is_object($config->host) ? null : $config->host,
                'port'       => \is_object($config->port) ? null : $config->port,
                'auth'       => \is_object($config->auth) ? null : $config->auth,
                'persistent' => \is_object($config->redis_persistent) ? null : $config->redis_persistent,
                'lifetime'   => \is_object($config->redis_lifetime) ? null : $config->redis_lifetime,
                'prefix'     => 'session',
                'index'      => \is_object($config->redis_dbname) ? null : $config->redis_dbname,
            ]
        );
        $session->start();

        return $session;
    }
}
