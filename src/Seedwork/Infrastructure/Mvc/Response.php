<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

abstract class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private array $headers,
        private StatusCode $statusCode,
        public readonly object $data,
    ) {
    }

    abstract public function getBody(): string;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode->value;
    }
}
