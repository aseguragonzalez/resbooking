<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Routes;

final class Path
{
    private function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Path $path): bool
    {
        return $this->value === $path->value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public static function create(string $value): self
    {
        return new self($value);
    }
}
