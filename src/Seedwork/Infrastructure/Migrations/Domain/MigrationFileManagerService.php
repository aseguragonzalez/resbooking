<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain;

use Seedwork\Infrastructure\Files\FileManager;
use Seedwork\Infrastructure\Migrations\Domain\Migration;
use Seedwork\Infrastructure\Migrations\Domain\Script;

final readonly class MigrationFileManagerService implements MigrationFileManager
{
    public function __construct(private FileManager $fileManager)
    {
    }

    /**
     * @return array<Migration>
     */
    public function getMigrations(string $basePath): array
    {
        $folders = $this->fileManager->getFoldersFromPath($basePath);
        return array_map(function (string $folder) use ($basePath) {
            $folderPath = "{$basePath}/{$folder}";
            $files = $this->fileManager->getFileNamesFromPath(
                $folderPath,
                extensions: ['sql'],
                notEndsWith: ['rollback']
            );
            $scripts = array_map(fn ($script) => Script::fromFile($folderPath, $script, $this->fileManager), $files);
            return Migration::new(name: $folder, scripts: $scripts);
        }, $folders);
    }
}
