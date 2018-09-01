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

require __DIR__.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'autoload.php';
\chdir(__DIR__.\DIRECTORY_SEPARATOR.'public');
$app = new Interna(false, true, 7, true);
$config = $app->di->get('config');
try {
$connection = \json_decode(\json_encode($config->database->connection), true);
} catch (Throwable $exception)
{
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new \Interna\Core\Exception\InvalidConfigurationException('Database Configuration is invalid!');
}

return \array_replace(
    [
        'environments' => [
                'default_migration_table' => 'interna_setup',
                'default_database'        => $connection->dbname,
                'production'              => [
                        'adapter' => $connection->adapter,
                        'host'    => $connection->host,
                        'name'    => $connection->dbname,
                        'user'    => $connection->user,
                        'pass'    => $connection->pass,
                        'port'    => $connection->port,
                    ],
            ],
        'paths' => [
                'migrations' => CODE.DS.'*'.DS.'*'.DS.'Setup'.DS.'Migrations',
                'seeds'      => CODE.DS.'*'.DS.'*'.DS.'Setup'.DS.'Seeds',
            ],
        'version_order'        => 'creation',
        'migration_base_class' => '\Interna\Core\Setup\AbstractMigration',
    ],
    \json_decode(\json_encode($config->migrations), true) ?? []
);
