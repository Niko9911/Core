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
use Phalcon\Mvc\Router\Group;

abstract class AbstractRouter extends Group
{
    private $namespaceCache = [];

    /** @var bool If you want extend module */
    protected $fullModulePath = false;

    /** @var string If you want extend module */
    protected $controllersNamespace = '';

    /**
     * AbstractRouter constructor.
     *
     * @param null $paths
     *
     * @throws NamespaceParseException
     */
    public function __construct($paths = null)
    {
        $namespace = $this->getVendorAndModule();

        if (false === $this->fullModulePath) {
            $module = $namespace['vendor'].'_'.$namespace['module'].'_';
        }

        $this->setPaths(
            [
                'module'    => $this->fullModulePath ? $this->setModule() : ($module ?? '').$this->setModule(),
                'namespace' => '' === $this->controllersNamespace ? $namespace['vendor'].'\\'.$namespace['module'].
                '\\Controllers' : $this->controllersNamespace,
            ]
        );
        parent::__construct($paths);
    }

    /**
     * @return string Underscored path to module. Example: Example_Welcome_ExampleModule
     */
    abstract protected function setModule(): string;

    abstract public function initialize(): void;

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
