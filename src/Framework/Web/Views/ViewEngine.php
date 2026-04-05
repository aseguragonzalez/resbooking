<?php

declare(strict_types=1);

namespace Framework\Views;

use Framework\Actions\Responses\View;
use Framework\Requests\RequestContext;

interface ViewEngine
{
    public function render(View $view, RequestContext $context): string;
}
