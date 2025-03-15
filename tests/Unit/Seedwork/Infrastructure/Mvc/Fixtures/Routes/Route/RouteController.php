<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Route;

use Seedwork\Infrastructure\Mvc\Controllers\Controller;
use Seedwork\Infrastructure\Mvc\Responses\Response;

final class RouteController extends Controller
{
    public function get(): Response
    {
        return $this->view();
    }
}
