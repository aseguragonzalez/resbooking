<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;

final class ResetPasswordChallenge extends FormModel
{
    /**
     * @param array<string, string> $errors
     */
    private function __construct(public readonly string $token, array $errors = [])
    {
        parent::__construct(pageTitle: '{{accounts.reset-password.title}}', errors: $errors);
    }

    public static function new(string $token): self
    {
        return new self(token: $token);
    }

    /**
     * @param array<string, string> $errors
     */
    public static function withErrors(array $errors, string $token): self
    {
        return new self(token: $token, errors: $errors);
    }
}
