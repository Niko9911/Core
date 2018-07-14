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

final class Request extends \Phalcon\Http\Request
{
    /**
     * Description: Return Patch request output.
     *
     * @param string $name
     * @param mixed  $filters
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getPatch(string $name, $filters = null, $defaultValue = false)
    {
        $items = [];
        if ($this->isPatch()) {
            $filter = $this->getDI()->get('filter');
            foreach ($this->getArrayFromRawBody() as $key => $value) {
                if ($name && $name !== $key) {
                    continue;
                }
                if ($filters) {
                    if (\is_array($filters)) {
                        foreach ($filters as $filter) {
                            $value = $filter->sanitize($value, $filter);
                        }
                    } else {
                        $value = $filter->sanitize($value, $filters);
                    }
                }
                $items[$key] = $value;
            }
        }

        return \urldecode($name ? $items[$name] : $items) ?? $defaultValue;
    }

    /**
     * Description: Does request have patch content?
     *
     * @param $name
     *
     * @return bool
     */
    public function hasPatch($name): bool
    {
        $has = false;
        if ($this->isPatch()) {
            foreach ($this->getArrayFromRawBody() as $key => $value) {
                if ($name && $name === $key) {
                    $has = true;
                }
            }
        }

        return $has;
    }

    /**
     * @return array
     */
    private function getArrayFromRawBody(): array
    {
        $items = [];
        $raw = $this->getRawBody();
        foreach (\explode('&', $raw) as $pair) {
            [$k, $v] = \explode('=', $pair);
            $items[$k] = $v;
        }

        return $items;
    }
}
