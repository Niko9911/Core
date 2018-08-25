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
final class Interna extends \Phalcon\Mvc\User\Component
{
    private static $USE_CACHE = false;
    private static $DEBUG = true;
    private static $CLI = false;

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
    private static $LOG_LEVEL = 7;

    private static $unitTestSetup = false;

    /** @var \Phalcon\DiInterface */
    private $localDI;

    /** @var \Interna\Core\Config */
    private $config;

    /** @var Phalcon\Mvc\Application */
    private $application;

    public function __construct(bool $cacheXML = false, bool $debug = false, int $logLevel = 5, bool $cli = false)
    {
        self::$USE_CACHE = $cacheXML;
        self::$DEBUG = $debug;
        self::$LOG_LEVEL = $logLevel;
        self::$CLI = $cli;

        $this->xdebug();
        if (!self::$unitTestSetup) {
            $this->define();
            $this->loadFiles();
        }
        $this->construct();

        try {
            if (self::$USE_CACHE) {
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
            $this->registerDefines();
            $this->registerModules();
            $this->registerRoutes();
            $this->registerCommandBus();
            if (self::$USE_CACHE) {
                $this->cacheConfigs();
            }
            if (!self::$CLI) {
                $this->run();
            } else {
                self::$unitTestSetup = true;
            }
        } catch (\Throwable $e) {
            /* @noinspection DegradedSwitchInspection */
            switch ($e) {
                case 'Service \'view\' wasn\'t found in the dependency injection container' === $e->getMessage():
                    try {
                        throw new \Interna\Core\Exception\UnhandledNotFoundException("You're missing probably
                    'view' service from DI or you're missing 404 handler when no routes are matched.");
                    } catch (Throwable $exception) {
                        $this->handleException($exception);
                    }
                    break;
                default:
                    $this->handleException($e);
                    break;
            }
        }
    }

    private function cacheConfigs(): bool
    {
        if (self::$USE_CACHE) {
            return (bool)\file_put_contents(CACHE.DS.'config.cache', \serialize($this->config->export()));
        }

        return false;
    }

    private function loadCacheConfigs(): bool
    {
        if (self::$USE_CACHE && \file_exists(CACHE.DS.'config.cache')) {
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

    private function loadFiles(): void
    {
        /** @noinspection PhpIncludeInspection */
        require CODE.DS.'Interna'.DS.'Core'.DS.'Config.php';
        /** @noinspection PhpIncludeInspection */
        require CODE.DS.'Interna'.DS.'Core'.DS.'Autoloader.php';
        /** @noinspection PhpIncludeInspection */
        include BASE.DS.'vendor'.DS.'autoload.php';
    }

    private function construct(): void
    {
        $this->localDI = new Phalcon\DI\FactoryDefault();
        $this->config = new Interna\Core\Config(CODE.DS.'System'.DS.'Phalcon');
        $this->localDI->set('log', function () {
            $logger = new Phalcon\Logger\Multiple();
            $logger->setLogLevel(self::$LOG_LEVEL);
            $logger->push(new \Phalcon\Logger\Adapter\File(LOG.DS.'common.log'));

            return $logger;
        });
    }

    private function loadAppConfig(): void
    {
        \file_exists(ETC.DS.'config.xml') ? $this->config->addXml(ETC.DS.'config.xml') : null;
        \file_exists(ETC.DS.'local.xml') ? $this->config->addXml(ETC.DS.'local.xml') : null;
        \file_exists(ETC.DS.'define.xml') ? $this->config->addXml(ETC.DS.'define.xml') : null;
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

    /**
     * @throws \Interna\Core\Exception\ModuleIsNotPresentInPathException
     */
    private function handleModuleConfig(): void
    {
        $conf = $this->config->export('modules');
        $autoload = [];
        foreach ($conf as $name => $values) {
            $modName = \str_replace('_', DS, $name);

            // Check if Module is not enabled.
            if ('true' === \mb_strtolower($values['active'])) {   // Check if module requires prefix to namespace.
                if (isset($values['@attributes']['prefix'])) { // With Prefix.
                    $autoload[$values['@attributes']['prefix'].
                    \str_replace('_', '\\', $name)] = CODE.DS.$modName;
                } else { // Without Prefix.
                    $autoload[\str_replace('_', '\\', $name)] = CODE.DS.$modName;
                }
                $dbMigrationPaths = CODE.DS.$modName;
                if (isset($values['@attributes']['type']) && 'module' === \mb_strtolower($values['@attributes']['type'])) {
                    $register['module'][$name] = CODE.DS.$modName;
                    if (!\file_exists($register['module'][$name])) {
                        $path = $register['module'][$name];
                        $exceptionPath = CODE.DS.'Interna'.DS.'Core'.DS.'Exception'.DS;
                        /** @noinspection PhpIncludeInspection */
                        require_once $exceptionPath.'Exception.php';
                        require_once $exceptionPath.'ModuleIsNotPresentInPathException.php';
                        throw new \Interna\Core\Exception\ModuleIsNotPresentInPathException(
                            "Module $name is not found from path $path!"
                        );
                    }
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
                \define(\mb_strtoupper($name), $path);
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
                \defined('ROUTER_DEFAULT_MODULE') ? ROUTER_DEFAULT_MODULE : null,
                $register,
                \defined('ROUTER_DEFAULT_NOT_FOUND') ? ROUTER_DEFAULT_NOT_FOUND : null,
                false,
                \defined('ROUTER_REMOVE_EXTRA_SLASHES') ? (bool)ROUTER_REMOVE_EXTRA_SLASHES : true
            );
        }
    }

    private function registerCommandBus(): void
    {
        if (!isset($this->di->get('config')->command_bus)) {
            return;
        }
        $commandBus = $this->di->get('config')->command_bus;

        $this->di->set('bus', function () use ($commandBus): \Interna\Core\CommandBus\CommandBusInterface {
            return new \Interna\Core\CommandBus\CommandBus(
                new \Interna\Core\CommandBus\Locator\Handler\ConfigLocator($commandBus)
            );
        });
    }

    private function registerDefines(): void
    {
        $defines = $this->di->get('config')->define;
        unset($defines->comment);
        foreach ($defines as $key => $value) {
            if ($value instanceof \Phalcon\Config) {
                \define($key, (array)$value);
            } else {
                \define($key, $value);
            }
        }
    }

    private function handleException(Throwable $throwable): void
    {
        /** @var \Phalcon\Logger\Multiple $log */
        $log = $this->localDI->get('log');
        $log->critical((string)$throwable);
        if (self::$DEBUG) {
            $whoops = new \Whoops\Run();
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            $whoops->handleException($throwable);
        }
    }

    private function run(): void
    {
        echo $this->application->handle()->getContent();
    }

    public static function isDebug(): bool
    {
        return self::$DEBUG ?? true;
    }

    public static function getVersion(): string
    {
        return '1.0.0';
    }
}
