<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Controllers;

use Seedwork\Infrastructure\Mvc\Actions\Responses\ActionResponse;

final class SubTestController extends TestController
{
    public function index(): ActionResponse
    {
        return $this->view();
    }
}
