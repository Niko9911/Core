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

use Phalcon\Db\Adapter\Pdo\Factory;
use Phalcon\Db\AdapterInterface;
use Phalcon\Di;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\RouterInterface;
use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\Url as UrlResolver;

final class Services extends Component
{
    private static $configLoaded = false;

    public static function config(array $config, bool $merge = false, ?Di $di = null): void
    {
        if (self::$configLoaded && $merge) {
            /* @var \Phalcon\Config $config */
            if (null !== $di) {
                $config = $di->get('config');
            } else {
                $config = (new self())->getDI()->get('config');
            }
            $config->merge($config);

            return;
        }

        if (null !== $di) {
            $di->set('config', function () use ($config) {
                return new \Phalcon\Config($config);
            });
        } else {
            (new self())->getDI()->set('config', function () use ($config) {
                return new \Phalcon\Config($config);
            });
        }
    }

    public static function url(?string $basePath = null, ?string $baseUri = null, ?string $staticBaseUri = null): void
    {
        //The URL component is used to generate all kind of urls in the application
        (new self())->getDI()->setShared('url', function () use ($basePath, $baseUri, $staticBaseUri): UrlResolver {
            $url = new UrlResolver();
            null === $basePath ?: $url->setBasePath($basePath);
            null === $baseUri ?: $url->setBaseUri($baseUri);
            null === $staticBaseUri ?: $url->setStaticBaseUri($staticBaseUri);

            return $url;
        });
    }

    /**
     * Description:.
     * [
     *    'adapter'     => 'Mysql',
     *    'host'        => $config->database->sql->host,
     *    'username'    => \getenv($username),
     *    'password'    => \getenv($password),
     *    'dbname'      => \getenv($database),
     *    'charset'     => 'UTF8',
     *  ].
     *
     * @param array   $options
     * @param null|Di $di
     */
    public static function db(array $options, ?Di $di = null): void
    {
        // Setup database adapter.
        if (null !== $di) {
            $di->set('db', function () use ($options) {
                return Factory::load($options);
            });

            return;
        }
        (new self())->getDI()->set('db', function () use ($options): AdapterInterface {
            return Factory::load($options);
        });
    }

    public static function router(?string $defaultModule = null, ?array $subRouters = null, ?array $notFound = null, bool $defaultRoutes = false, bool $removeExtraSlashes = true): void
    {
        // Load router service.
        (new self())->getDI()->set('router', function () use ($defaultModule,
            $defaultRoutes, $subRouters, $removeExtraSlashes, $notFound): RouterInterface {
            // Initialize new router without default routes.
            $router = new Router($defaultRoutes);
            $router->removeExtraSlashes($removeExtraSlashes);
            $router->setDefaultModule($defaultModule);

            // Mount Sub-Routers.
            if (null !== $subRouters) {
                foreach ($subRouters as $subRouter) {
                    $router->mount(new $subRouter());
                }
            }

            // System routes.
            if (null !== $notFound) {
                $router->notFound($notFound);
            }

            // Return routes.
            return $router;
        });
    }
}
