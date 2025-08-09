<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

interface ContentReplacer
{
    public function replace(object $model, string $template): string;
}
