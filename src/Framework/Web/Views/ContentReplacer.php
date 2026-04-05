<?php

declare(strict_types=1);

namespace Framework\Web\Views;

use Framework\Web\Requests\RequestContext;

interface ContentReplacer
{
    /**
     * @param array<string, mixed>|object|null $model
     */
    public function replace(array|object|null $model, string $template, RequestContext $context): string;
}
