<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

abstract class Header
{
    public function __construct(public readonly string $name, public readonly string $value)
    {
    }

    public function __toString(): string
    {
        return "{$this->name}: {$this->value}";
    }

    public function equals(Header $header): bool
    {
        return $this->name === $header->name && $this->value === $header->value;
    }
}
