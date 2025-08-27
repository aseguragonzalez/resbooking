<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

interface ContentReplacer
{
    public function replace(object $model, string $template, RequestContext $context): string;
}
