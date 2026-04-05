<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\RequestResetPassword;

final readonly class RequestResetPasswordCommand
{
    public function __construct(
        public string $username,
    ) {
    }
}
