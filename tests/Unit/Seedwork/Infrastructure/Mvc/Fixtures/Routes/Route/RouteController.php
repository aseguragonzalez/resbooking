<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Route;

use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;

final class RouteController extends Controller
{
    public function get(): ActionResponse
    {
        return $this->view();
    }
}
