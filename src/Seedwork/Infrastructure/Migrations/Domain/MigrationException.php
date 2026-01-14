<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain;

use Seedwork\Infrastructure\Migrations\Domain\Script;

final class MigrationException extends \Exception
{
    /**
     * @param array<Script> $scripts
     */
    public function __construct(
        public readonly array $scripts,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
