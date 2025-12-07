<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

final readonly class SignUpRequest
{
    public function __construct(
        public string $username,
        public string $password,
        public string $passwordConfirm,
        public string $agree = 'off',
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->username)) {
            $errors['username'] = '{{accounts.signup.form.username.error.required}}';
        } elseif (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            $errors['username'] = '{{accounts.signup.form.username.error.email}}';
        }

        if (empty($this->password)) {
            $errors['password'] = '{{accounts.signup.form.password.error.required}}';
        } elseif (
            strlen($this->password) < 8 ||
            !preg_match('/[A-Za-z]/', $this->password) ||
            !preg_match('/\d/', $this->password) ||
            !preg_match('/[^A-Za-z\d]/', $this->password)
        ) {
            $errors['password'] = '{{accounts.signup.form.password.error.weak}}';
        }

        if ($this->password !== $this->passwordConfirm) {
            $errors['passwordConfirm'] = '{{accounts.signup.form.password-confirm.error.mismatch}}';
        }

        if ($this->agree !== 'on') {
            $errors['agree'] = '{{accounts.signup.form.agree.error.required}}';
        }

        return $errors;
    }
}
