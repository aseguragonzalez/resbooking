<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Fixtures\Controllers;

use Framework\Web\Actions\MvcAction;
use Framework\Web\Actions\Responses\ActionResponse;

final class SubTestController extends TestController
{
    public function __construct()
    {
        parent::__construct();
    }

    #[MvcAction]
    public function index(): ActionResponse
    {
        return $this->view('SubTest/index');
    }
}
