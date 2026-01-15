<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain\Entities;

use DateTimeImmutable;

final readonly class Migration
{
    /**
     * @param array<Script> $scripts
     */
    private function __construct(
        public string $name,
        public DateTimeImmutable $createdAt,
        public array $scripts = [],
    ) {
    }

    /**
     * @param array<Script> $scripts
     */
    public static function new(string $name, array $scripts, ?DateTimeImmutable $createdAt = null): self
    {
        $createdAt = $createdAt ?? new DateTimeImmutable("now", new \DateTimeZone("UTC"));

        return new self(name: $name, createdAt: $createdAt, scripts: $scripts);
    }

    /**
     * @param array<Script> $scripts
     */
    public static function build(DateTimeImmutable $createdAt, string $name, array $scripts): self
    {
        return self::new(name: $name, createdAt: $createdAt, scripts: $scripts);
    }
}
