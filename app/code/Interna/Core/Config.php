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

final class Config
{
    private $data = [];
    private $phalcon;

    /**
     * Config constructor.
     *
     * @param string $phalconRoot
     */
    public function __construct(string $phalconRoot)
    {
        $this->phalcon = $phalconRoot;
    }

    /**
     * @param string $jsonPath
     * @param bool   $file
     *
     * @return Config
     */
    public function addJson(string $jsonPath, bool $file = true): self
    {
        if ($file) {
            $jsonPath = \file_get_contents($jsonPath);
        }

        $this->data = \array_merge_recursive($this->data, \json_decode($jsonPath, true));
        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw new \RuntimeException('Error: '.\json_last_error_msg());
        }

        return $this;
    }

    /**
     * @param string $xmlPath
     *
     * @return Config
     */
    public function addXml(string $xmlPath): self
    {
        $this->addJson(\json_encode(\simplexml_load_string(\file_get_contents($xmlPath))), false);

        return $this;
    }

    /**
     * @param string $yamlPath
     *
     * @return Config
     */
    public function addYaml(string $yamlPath): self
    {
        $this->data = \array_merge($this->data, \yaml_parse_file($yamlPath));

        return $this;
    }

    /**
     * @param null|string $key
     *
     * @return array|null
     */
    public function export(?string $key = null): ?array
    {
        if (null !== $key) {
            return $this->data[$key];
        }

        return $this->data;
    }

    /**
     * @return array
     */
    public function exportAutoload(): array
    {
        return $this->data['autoload'];
    }

    public function import(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $data
     * @param bool  $recursive
     *
     * @return Config
     */
    public function merge(array $data, $recursive = true): self
    {
        if ($recursive) {
            $this->data = \array_merge_recursive($this->data, $data);
        } else {
            $this->data = \array_merge($this->data, $data);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPhalcon(): string
    {
        return $this->phalcon;
    }
}
