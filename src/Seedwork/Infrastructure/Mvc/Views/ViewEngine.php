<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Actions\Responses\View;

interface ViewEngine
{
    public function render(View $view): string;
}
