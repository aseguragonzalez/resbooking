<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

final readonly class ConfirmResetPasswordRequest
{
    public function __construct(
        public string $token,
        public string $newPassword
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->token)) {
            $errors['token'] = '{{accounts.reset-password.form.token.error.required}}';
        }

        if (empty($this->newPassword)) {
            $errors['newPassword'] = '{{accounts.reset-password.form.new-password.error.required}}';
        } elseif (
            strlen($this->newPassword) < 8 ||
            !preg_match('/[A-Za-z]/', $this->newPassword) ||
            !preg_match('/\d/', $this->newPassword) ||
            !preg_match('/[^A-Za-z\d]/', $this->newPassword)
        ) {
            $errors['newPassword'] = '{{accounts.reset-password.form.new-password.error.weak}}';
        }

        return $errors;
    }
}
