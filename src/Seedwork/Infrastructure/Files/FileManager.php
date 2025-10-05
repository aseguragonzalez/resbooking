<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Files;

interface FileManager
{
    public function readTextPlain(string $path): string;

    /**
    * @return array<string, string>
    */
    public function readKeyValueJson(string $path): array;
}
