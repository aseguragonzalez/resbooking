<?php

declare(strict_types=1);

namespace Framework\Mvc\Views;

use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\Requests\RequestContext;

interface ViewEngine
{
    public function render(View $view, RequestContext $context): string;
}
