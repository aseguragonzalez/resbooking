<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

final class ResetPasswordRequest
{
    public function __construct(public readonly string $username)
    {
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->username)) {
            $errors['username'] = '{{accounts.reset-password.form.username.error.required}}';
        } elseif (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            $errors['username'] = '{{accounts.reset-password.form.username.error.invalid}}';
        }

        return $errors;
    }
}
