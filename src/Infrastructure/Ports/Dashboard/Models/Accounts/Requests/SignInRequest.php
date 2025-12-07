<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

final readonly class SignInRequest
{
    public function __construct(
        public string $username,
        public string $password,
        public string $rememberMe = 'off',
    ) {
    }

    public function keepMeSignedIn(): bool
    {
        return $this->rememberMe === 'on';
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->username)) {
            $errors['username'] = '{{accounts.signin.form.username.error.required}}';
        } elseif (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            $errors['username'] = '{{accounts.signin.form.username.error.invalid_email}}';
        }

        if (empty($this->password)) {
            $errors['password'] = '{{accounts.signin.form.password.error.required}}';
        }

        return $errors;
    }
}
