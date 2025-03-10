<?php

declare(strict_types=1);

namespace App\Domain\Users\ValueObjects;

use App\Domain\Shared\Password;
use Seedwork\Domain\ValueObject;
use Seedwork\Domain\Exceptions\ValueException;
use Tuupola\Ksuid;

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

    public static function new(Password $password, string $seed = ''): self
    {
        $seed = empty($seed) ? (string)new Ksuid() : $seed;
        $secret = Credential::getSecret($password, $seed);
        return new Credential($secret, $seed);
    }

    private static function getSecret(Password $password, string $seed): string
    {
        if (empty($password->getValue())) {
            throw new ValueException("Password cannot be empty.");
        }

        if (empty($seed)) {
            throw new ValueException("Seed cannot be empty.");
        }

        return hash('sha512', "{$password->getValue()}.{$seed}");
    }

    public function check(Password $password): bool
    {
        if (empty($password->getValue())) {
            throw new ValueException("Password cannot be empty.");
        }
        return $this->secret === Credential::getSecret($password, $this->seed);
    }
}
