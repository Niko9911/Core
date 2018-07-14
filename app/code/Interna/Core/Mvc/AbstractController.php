<?php

declare(strict_types=1);

/**
 * Interna — Club Management — NOTICE OF LICENSE
 * This source file is released under commercial license by Iron Lions.
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
    abstract public function beforeExecuteRoute(Dispatcher $dispatcher);

    /**
     * Description: Executed after every found action.
     *
     * @param $dispatcher
     */
    abstract public function afterExecuteRoute(Dispatcher $dispatcher);
}
