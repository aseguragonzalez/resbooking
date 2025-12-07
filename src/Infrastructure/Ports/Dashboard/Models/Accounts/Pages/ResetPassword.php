<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;

final readonly class ResetPassword extends FormModel
{
    /**
     * @param array<string, string> $errors
     */
    private function __construct(array $errors = [], public bool $hasSucceeded = false)
    {
        parent::__construct(pageTitle: '{{accounts.reset-password.title}}', errors: $errors);
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @param array<string, string> $errors
     */
    public static function withErrors(array $errors): self
    {
        return new self(errors: $errors);
    }

    public static function succeeded(): self
    {
        return new self(hasSucceeded: true);
    }
}
