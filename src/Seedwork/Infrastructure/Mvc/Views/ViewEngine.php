<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

interface ViewEngine
{
    public function render(View $view): string;
}
