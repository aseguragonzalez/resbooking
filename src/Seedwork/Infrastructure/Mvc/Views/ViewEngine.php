<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

interface ViewEngine
{
    public function render(View $view, RequestContext $context): string;
}
