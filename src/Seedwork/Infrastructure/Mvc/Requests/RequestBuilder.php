<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Requests;

interface RequestBuilder
{
    /**
     * @param class-string $requestType
     */
    public function withRequestType(string $requestType): RequestBuilder;

    /**
     * @param array<string, string|int|float> $args
     */
    public function withArgs(array $args): RequestBuilder;

    public function build(): object;
}
