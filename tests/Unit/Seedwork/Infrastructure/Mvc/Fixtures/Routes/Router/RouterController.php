<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Routes\Router;

use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;
use Seedwork\Infrastructure\Mvc\Controllers\Controller;

final class RouterController extends Controller
{
    public function get(): ActionResponse
    {
        return $this->view();
    }
}
