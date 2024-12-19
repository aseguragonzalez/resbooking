<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use App\Seedwork\Domain\ValueObject;
use App\Seedwork\Domain\Exceptions\ValueException;

final class Credential extends ValueObject
{
    private function __construct(public readonly string $secret, public readonly string $seed)
    {
        if (empty($secret)) {
            throw new ValueException("Secret cannot be empty.");
        }

        if (empty($seed)) {
            throw new ValueException("Seed cannot be empty.");
        }
    }

    public static function build(string $secret, string $seed): Self
    {
        return new Credential($secret, $seed);
    }

    public static function new(string $phrase, string $seed): Self
    {
        $secret = Credential::getSecret($phrase, $seed);
        return new Credential($secret, $seed);
    }

    private static function getSecret(string $phrase, string $seed): string
    {
        if (empty($phrase)) {
            throw new ValueException("Phrase cannot be empty.");
        }

        if (empty($seed)) {
            throw new ValueException("Seed cannot be empty.");
        }

        return "{$phrase}.{$seed}";
    }

    public function check(string $phrase): bool
    {
        if (empty($phrase)) {
            throw new ValueException("Phrase cannot be empty.");
        }

        return $this->secret === Credential::getSecret($phrase, $this->seed);
    }
}
