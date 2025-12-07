<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;

final readonly class SignIn extends FormModel
{
    /**
     * @param array<string, string> $errors
     */
    private function __construct(array $errors = [])
    {
        parent::__construct(pageTitle: '{{accounts.signin.title}}', errors: $errors);
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
}
