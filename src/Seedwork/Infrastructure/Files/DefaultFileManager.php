<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Files;

final class DefaultFileManager implements FileManager
{
    public function readTextPlain(string $path): string
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("Failed to read file: {$path}");
        }

        return $content;
    }

    /**
    * @return array<string, string>
    */
    public function readKeyValueJson(string $path): array
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException("Failed to read file: {$path}");
        }

        /** @var array<string, string> | null $dictionary */
        $dictionary = json_decode($content, true);
        if ($dictionary === null) {
            throw new \RuntimeException("Failed to decode JSON: " . json_last_error_msg());
        }

        return $dictionary;
    }
}
