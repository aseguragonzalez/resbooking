<?php

declare(strict_types=1);

namespace Framework;

use Framework\Web\AppFilesystemPath;

/**
 * Filesystem root for HTML templates resolved by {@see \Framework\Views\HtmlViewEngine}.
 */
final readonly class HtmlViewEngineSettings
{
    public string $path;

    public function __construct(string $basePath, string $viewPath = 'Views/')
    {
        $this->path = AppFilesystemPath::join($basePath, $viewPath);
    }
}
