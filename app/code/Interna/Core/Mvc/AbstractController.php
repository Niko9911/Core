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

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

abstract class AbstractController extends Controller
{
    /**
     * Description: Executed before every found action.
     *
     * @param $dispatcher
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher): void
    {
    }

    /**
     * Description: Executed after every found action.
     *
     * @param $dispatcher
     */
    public function afterExecuteRoute(Dispatcher $dispatcher): void
    {
    }
}
