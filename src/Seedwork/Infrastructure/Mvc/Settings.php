<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

class Settings
{
    public readonly string $viewPath;

    public function __construct(public readonly string $basePath, string $viewPath = '/Views')
    {
        $this->viewPath = $this->basePath . $viewPath;
    }
}
