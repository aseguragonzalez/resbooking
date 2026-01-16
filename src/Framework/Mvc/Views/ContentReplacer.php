<?php

declare(strict_types=1);

namespace Framework\Mvc\Views;

use Framework\Mvc\Requests\RequestContext;

interface ContentReplacer
{
    public function replace(?object $model, string $template, RequestContext $context): string;
}
