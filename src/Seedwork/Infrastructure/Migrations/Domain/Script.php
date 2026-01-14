<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain;

use Seedwork\Infrastructure\Files\FileManager;

final readonly class Script
{
    private function __construct(
        public string $fileName,
        public ?string $content = null,
        public ?string $rollbackContent = null,
    ) {
    }

    public static function fromFile(string $basePath, string $fileName, FileManager $fileManager): self
    {
        $filePath = "{$basePath}/{$fileName}";
        $content = $fileManager->readTextPlain($filePath);

        $rollbackFilePath = str_replace(".sql", ".rollback.sql", $filePath);
        $rollbackContent = $fileManager->readTextPlain($rollbackFilePath);
        return new self($fileName, $content, $rollbackContent);
    }

    public static function build(string $fileName): self
    {
        return new self($fileName);
    }

    /**
     * @return array<string>
     */
    public function getStatements(): array
    {
        if (!$this->isLoaded()) {
            throw new \RuntimeException("Script is not loaded");
        }

        return $this->getSqlStatements($this->content ?? '');
    }

    private function isLoaded(): bool
    {
        return $this->content !== null;
    }

    /**
     * @return array<string>
     */
    public function getRollbackStatements(): array
    {
        if (!$this->isRollbackLoaded()) {
            throw new \RuntimeException("Script is not loaded");
        }

        return $this->getSqlStatements($this->rollbackContent ?? '');
    }

    /**
     * @return array<string>
     */
    private function getSqlStatements(string $fileContent): array
    {
        // Get file lines without comments and empty lines
        $fileLines = array_filter(
            explode("\n", $fileContent),
            fn ($line) => !empty($line) && !str_starts_with(trim($line), '--')
        );
        // Split the file content into statements separated by semicolons
        $statements = array_filter(explode(';', implode('', $fileLines)), fn ($stmt) => !empty($stmt));
        // Remove empty statements and return the statements with a semicolon at the end
        return array_values(array_map(fn ($stmt) => trim($stmt) . ';', $statements));
    }

    private function isRollbackLoaded(): bool
    {
        return $this->rollbackContent !== null;
    }
}
