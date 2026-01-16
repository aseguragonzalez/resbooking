<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Fixtures\Controllers;

use Framework\Mvc\Actions\Responses\ActionResponse;

final class SubTestController extends TestController
{
    public function index(): ActionResponse
    {
        return $this->view();
    }
}
