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

namespace Interna\Core\Mvc;

use Interna\Core\Exception\NamespaceParseException;
use Interna\Core\View\Volt;
use Phalcon\Mvc\ModuleDefinitionInterface;

abstract class AbstractModule implements ModuleDefinitionInterface
{
    private $namespaceCache = [];

    /**
     * Registers an autoloader related to the module.
     *
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = null): void
    {
        // No need to implement, due everything should be already autoloaded.
    }

    /**
     * Registers services related to the module.
     *
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    abstract public function registerServices(\Phalcon\DiInterface $dependencyInjector): void;

    /**
     * @param \Phalcon\DiInterface $dependencyInjector
     *
     * @throws NamespaceParseException
     */
    protected function registerViewVolt(\Phalcon\DiInterface $dependencyInjector): void
    {
        $namespace = $this->getVendorAndModule();
        $dependencyInjector->set(
            'view',
            Volt::setup(
                \constant(
                    \mb_strtoupper($namespace['vendor'].'_'.$namespace['module'])
                ).DS.'Views'
            )
        );
    }

    /**
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    protected function registerViewNone(\Phalcon\DiInterface $dependencyInjector): void
    {
        $dependencyInjector->set('view', new \Phalcon\Mvc\View());
    }

    /**
     * @param \Phalcon\DiInterface $dependencyInjector
     *
     * @throws NamespaceParseException
     */
    protected function registerDispatcher(\Phalcon\DiInterface $dependencyInjector): void
    {
        $namespace = $this->getVendorAndModule();
        $dispatcher = new \Phalcon\Mvc\Dispatcher();
        $dispatcher->setDefaultNamespace(
            $namespace['vendor'].'\\'.
            $namespace['module'].'\\Controllers'
        );

        $dependencyInjector->set('dispatcher', $dispatcher);
    }

    private function getChildNamespace(int $key): ?string
    {
        if (empty($this->namespaceCache)) {
            $this->namespaceCache = \explode('\\', static::class);
        }

        return $this->namespaceCache[$key] ?? null;
    }

    /**
     * @return array
     *
     * @throws NamespaceParseException
     */
    private function getVendorAndModule(): array
    {
        $vendor = $this->getChildNamespace(0);
        $module = $this->getChildNamespace(1);
        if (null === $module || null === $vendor) {
            throw new NamespaceParseException('Parsing '.static::class.
                ' namespace for registering volt view service automatically failed.');
        }

        return ['vendor' => $vendor, 'module' => $module];
    }
}
