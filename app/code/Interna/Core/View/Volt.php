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

namespace Interna\Core\View;

use Phalcon\Di;
use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;

final class Volt extends Component
{
    public static function setup(string $viewsDir, ?callable $functions = null): View
    {
        // Setup Base View.
        $view = new View();
        $view->setViewsDir($viewsDir);

        $settings = Di::getDefault()->get('config')->volt;

        // Load Volt.
        $view->registerEngines([
            '.volt' => function ($view) use ($functions, $settings): VoltEngine {
                /** @noinspection PhpUndefinedMethodInspection */
                $volt = new VoltEngine($view);
                $volt->setOptions(\array_replace([
                    'compiledPath'      => CACHE.DS,
                    'compiledSeparator' => '_',
                    'compiledExtension' => '.voltc',
                    'compileAlways'     => \index::isDebug(),
                    'prefix'            => 'volt-',
                    'autoescape'        => true,
                ], \array_map(function ($value) {
                    $value = \mb_strtolower($value);
                    if ('true' === $value || 'false' === $value) {
                        return (bool)$value;
                    }
                    if (\is_object($value)) {
                        return (array)$value;
                    }

                    return $value;
                }, (array)$settings->options)));

                $compiler = $volt->getCompiler();

                if (null !== $functions) {
                    $functions($compiler);
                }

                if (null !== $settings->extension) {
                    foreach ($settings->extension as $extension) {
                        $compiler->addExtension($extension);
                    }
                }

                if (null !== $settings->extension) {
                    foreach ($settings->functions as $name => $callable) {
                        $compiler->addFunction($name, $callable);
                    }
                }

                if (null !== $settings->filters) {
                    foreach ($settings->filters as $name => $callable) {
                        $compiler->addFilter($name, $callable);
                    }
                }

                return $volt;
            },
        ]);

        return $view;
    }
}
