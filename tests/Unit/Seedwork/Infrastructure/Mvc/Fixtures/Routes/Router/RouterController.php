<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Router;

use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Responses\Response;

final class RouterController extends Controller
{
    public function get(): Response
    {
        return $this->view();
    }
}
