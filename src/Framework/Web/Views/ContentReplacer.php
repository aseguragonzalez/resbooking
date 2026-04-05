<?php

declare(strict_types=1);

namespace Framework\Views;

use Framework\Requests\RequestContext;

interface ContentReplacer
{
    /**
     * @param array<string, mixed>|object|null $model
     */
    public function replace(array|object|null $model, string $template, RequestContext $context): string;
}
