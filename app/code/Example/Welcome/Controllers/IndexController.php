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

namespace Example\Welcome\Controllers;

use Interna\Core\Mvc\AbstractController;

final class IndexController extends AbstractController
{
    public function indexAction(): void
    {
    }

    public function notFoundAction(): void
    {
        \http_response_code(404);
        $this->view->pick('index/notFound');
    }
}
