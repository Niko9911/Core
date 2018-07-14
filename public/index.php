<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
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
    private const USE_CACHE = false;
    private const DEBUG = true;

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

    /** @var \Phalcon\DiInterface */
    private $localDI;

    /** @var \Interna\Core\Config */
    private $config;

    /** @var Phalcon\Mvc\Application */
    private $application;

    public function __construct()
    {
        $this->xdebug();
        $this->define();
        $this->construct();

        try {
            if (self::USE_CACHE) {
                if (!$this->loadCacheConfigs()) {
                    $this->loadAppConfig();
                    $this->loadModuleConfig();
                    $this->handleModuleConfig();
                }
            } else {
                $this->loadAppConfig();
                $this->loadModuleConfig();
                $this->handleModuleConfig();
            }
            $this->warmUp();
            $this->registerModules();
            $this->registerRoutes();
            $this->registerCommandBus();
            $this->cacheConfigs();
            $this->run();
        } catch (\Throwable $e) {
            /** @var \Phalcon\Logger\Multiple $log */
            $log = $this->localDI->get('log');
            $log->critical((string)$e);
            if (self::DEBUG) {
                $whoops = new \Whoops\Run();
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
                $whoops->handleException($e);
            }
        }
    }

    private function cacheConfigs(): bool
    {
        if (self::USE_CACHE) {
            return (bool)\file_put_contents(CACHE.DS.'config.cache', \serialize($this->config->export()));
        }

        return false;
    }

    private function loadCacheConfigs(): bool
    {
        if (self::USE_CACHE && \file_exists(CACHE.DS.'config.cache')) {
            $configs = \file_get_contents(CACHE.DS.'config.cache');
            if (false === $configs) {
                return false;
            }
            try {
                $configs = \unserialize($configs, ['allowed_classes' => false]);
            } catch (Throwable $e) {
                return false;
            }
            $this->config->import($configs);

            return true;
        }

        return false;
    }

    private function xdebug(): void
    {
        if (\extension_loaded('xdebug')) {
            \ini_set('xdebug.var_display_max_depth', '-1');
            \ini_set('xdebug.var_display_max_children', '-1');
            \ini_set('xdebug.var_display_max_data', '-1');
        }
    }

    private function define(): void
    {
        // Tells it's ran via index.php correctly.
        \define('RUNTIME', true);
        \define('DS', \DIRECTORY_SEPARATOR);

        // Define some absolute path constants.
        \define('BASE', \dirname(\getcwd()));
        \define('APP', BASE.DS.'app');
        \define('ETC', APP.DS.'etc');
        \define('CODE', APP.DS.'code');
        \define('I18N', APP.DS.'locale');
        \define('MODULES', ETC.DS.'modules');
        \define('VARDIR', BASE.DS.'var');
        \define('CACHE', VARDIR.DS.'cache');
        \define('LOG', VARDIR.DS.'log');
    }

    private function construct(): void
    {
        require CODE.DS.'Interna'.DS.'Core'.DS.'Config.php';
        require CODE.DS.'Interna'.DS.'Core'.DS.'Autoloader.php';
        include BASE.DS.'vendor'.DS.'autoload.php';
        $this->localDI = new Phalcon\DI\FactoryDefault();
        $this->config = new Interna\Core\Config(CODE.DS.'System'.DS.'Phalcon');
        $this->localDI->set('log', function () {
            $logger = new Phalcon\Logger\Multiple();
            $logger->setLogLevel(self::LOG_LEVEL);
            $logger->push(new \Phalcon\Logger\Adapter\File(LOG.DS.'common.log'));

            return $logger;
        });
    }

    private function loadAppConfig(): void
    {
        \file_exists(ETC.DS.'config.xml') ? $this->config->addXml(ETC.DS.'config.xml') : null;
        \file_exists(ETC.DS.'local.xml') ? $this->config->addXml(ETC.DS.'local.xml') : null;
    }

    private function loadModuleConfig(): void
    {
        // Load etc/modules/*.xml
        $files = \glob(MODULES.DS.'*.xml');
        foreach ($files as $file) {
            $this->config->addXml($file);
        }
        unset($files);
    }

    private function handleModuleConfig(): void
    {
        $conf = $this->config->export('modules');
        $autoload = [];
        foreach ($conf as $name => $values) {
            $modName = \str_replace('_', DS, $name);

            // Check if Module is not enabled.
            if (true === (bool)$values['active']) {   // Check if module requires prefix to namespace.
                if (isset($values['@attributes']['prefix'])) { // With Prefix.
                    $autoload[$values['@attributes']['prefix'].
                    \str_replace('_', '\\', $name)] = CODE.DS.$modName;
                } else { // Without Prefix.
                    $autoload[\str_replace('_', '\\', $name)] = CODE.DS.$modName;
                }

                if (isset($values['@attributes']['type']) && 'module' === \mb_strtolower($values['@attributes']['type'])) {
                    $register['module'][$name] = CODE.DS.$modName;
                    /** @noinspection OneTimeUseVariablesInspection */
                    $files = \glob(CODE.DS.$modName.DS.'etc'.DS.'*.xml');
                    foreach ($files as $file) {
                        $this->config->addXml($file);
                    }
                } else {
                    $register['lib'][$name] = CODE.DS.$modName;
                }
            }
        }
        $this->config->merge(['autoload' => ['namespace' => $autoload]]);
        $this->config->merge(['register' => $register ?? []]);
    }

    private function warmUp(): void
    {
        \Interna\Core\Autoloader::autoload($this->config);
        \Interna\Core\Services::config($this->config->export());
        \Interna\Core\Services::url();
        \Interna\Core\Services::db((array)$this->localDI->get('config')->database->connection);
        \Interna\Core\Checks::isWritableArray([CACHE, LOG]);
        $this->application = new Phalcon\Mvc\Application($this->localDI);
    }

    private function registerModules(): void
    {
        $mods = $this->di->get('config')->register->module;
        if (null !== $mods) {
            foreach ($this->di->get('config')->register->module as $name => $path) {
                $modules = $this->di->get('config')->modules->$name->module;
                $namespace = \str_replace('_', '\\', $name);
                if (\is_object($modules)) {
                    foreach ($modules as $module) {
                        $register[$name.'_'.$module] =
                            [
                                'className' => $namespace.'\Modules\\'.$module,
                                'path'      => $path.DS.'Modules'.DS.$module.'.php',
                            ];
                    }
                } else {
                    $register[$name.'_'.$modules] =
                        [
                            'className' => $namespace.'\Modules\\'.$modules,
                            'path'      => $path.DS.'Modules'.DS.$modules.'.php',
                        ];
                }
                unset($namespace, $name, $path, $module, $modules);
            }
            $this->application->registerModules($register ?? []);
        }
    }

    private function registerRoutes(): void
    {
        $mods = $this->di->get('config')->register->module;
        if (null !== $mods) {
            $register = null;
            foreach ($this->di->get('config')->register->module as $name => $path) {
                $routers = $this->di->get('config')->modules->$name->router;
                $namespace = \str_replace('_', '\\', $name);
                if (\is_object($routers)) {
                    foreach ($routers as $router) {
                        $register[] = $namespace.'\Routes\\'.$router;
                    }
                } else {
                    $register[] = $namespace.'\Routes\\'.$routers;
                }
                unset($namespace, $name, $path, $router, $routers);
            }
            \Interna\Core\Services::router(
                null,
                $register,
                null,
                false,
                true
            );
        }
    }

    private function registerCommandBus(): void
    {
        $commandBus = $this->di->get('config')->command_bus;
        $this->di->set('bus', function () use ($commandBus): \Interna\Core\CommandBus\CommandBusInterface {
            return new \Interna\Core\CommandBus\CommandBus(
                new \Interna\Core\CommandBus\Locator\Handler\ConfigLocator($commandBus)
            );
        });
    }

    private function run(): void
    {
        echo $this->application->handle()->getContent();
    }

    public static function isDebug(): bool
    {
        return self::DEBUG ?? true;
    }
}

new index();
