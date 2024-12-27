<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use App\Domain\Shared\Password;
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

    public static function build(string $secret, string $seed): self
    {
        return new Credential($secret, $seed);
    }

    public static function new(Password $phrase, string $seed): self
    {
        $secret = Credential::getSecret($phrase->getValue(), $seed);
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
