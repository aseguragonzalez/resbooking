<?php

declare(strict_types=1);

namespace Framework\Web\Views;

use Framework\Web\Actions\Responses\View;
use Framework\Web\Requests\RequestContext;

interface ViewEngine
{
    public function render(View $view, RequestContext $context): string;
}
