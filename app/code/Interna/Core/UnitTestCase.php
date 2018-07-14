<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
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

use Interna\Core\CommandBus\CommandBus;
use Interna\Core\CommandBus\CommandBusInterface;
use Interna\Core\CommandBus\Locator\Handler\ConfigLocator;
use Phalcon\Di;
use PHPUnit\Framework\IncompleteTestError;

abstract class UnitTestCase extends \Phalcon\Test\PHPUnit\UnitTestCase
{
    /** @var bool */
    private $loaded = false;

    /** @var \Phalcon\DiInterface */
    private $localDI;

    /** @var \Interna\Core\Config */
    private $localConfig;

    /**
     * UnitTestCase constructor.
     *
     * @param null|string $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->localDI = Di::getDefault();
        $this->xdebug();
        $this->define();
        $this->construct();
        $this->loadAppConfig();
        $this->loadModuleConfig();
        $this->handleModuleConfig();
        $this->warmUp();
        $this->registerCommandBus();
        $this->setUp();
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
        \define('BASE', \getcwd());
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
        $this->localDI = new \Phalcon\DI\FactoryDefault();
        $this->localConfig = new Config(CODE.DS.'System'.DS.'Phalcon');
        $this->localDI->set('log', function () {
            $logger = new \Phalcon\Logger\Multiple();
            $logger->setLogLevel(9);
            $logger->push(new \Phalcon\Logger\Adapter\File(LOG.DS.'common.log'));

            return $logger;
        });
    }

    private function loadAppConfig(): void
    {
        \file_exists(ETC.DS.'config.xml') ? $this->localConfig->addXml(ETC.DS.'config.xml') : null;
        \file_exists(ETC.DS.'local.xml') ? $this->localConfig->addXml(ETC.DS.'local.xml') : null;
    }

    private function loadModuleConfig(): void
    {
        // Load etc/modules/*.xml
        $files = \glob(MODULES.DS.'*.xml');
        foreach ($files as $file) {
            $this->localConfig->addXml($file);
        }
        unset($files);
    }

    private function handleModuleConfig(): void
    {
        $conf = $this->localConfig->export('modules');
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
                        $this->localConfig->addXml($file);
                    }
                } else {
                    $register['lib'][$name] = CODE.DS.$modName;
                }
            }
        }
        $this->localConfig->merge(['autoload' => ['namespace' => $autoload]]);
    }

    private function warmUp(): void
    {
        Autoloader::autoload($this->localConfig);
        Services::config($this->localConfig->export());
        Services::db((array)$this->localDI->get('config')->database->connection, $this->localDI);
        Checks::isWritableArray([CACHE, LOG]);
    }

    private function registerCommandBus(): void
    {
        $commandBus = $this->localDI->get('config')->command_bus ?? null;
        if ($commandBus !== null)
        {
            $this->localDI->set('bus', function () use ($commandBus): CommandBusInterface {
                return new CommandBus(
                    new ConfigLocator($commandBus)
                );
            });
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->setDI($this->localDI);
        $this->loaded = true;
    }

    /**
     * @author: Niko Grano <niko@ironlions.fi>
     * Description: Check if the test case is setup properly.
     *
     * @throws \PHPUnit\Framework\IncompleteTestError
     */
    public function __destruct()
    {
        if (!$this->loaded) {
            throw new IncompleteTestError('Please run parent::setUp().');
        }
    }
}




// then fetch and close the statement