<?php

declare(strict_types=1);

namespace Framework\Mvc;

final readonly class HtmlViewEngineSettings
{
    public string $path;

    public function __construct(string $basePath, string $viewPath = 'Views/')
    {
        $this->path = "{$basePath}/{$viewPath}";
    }
}
