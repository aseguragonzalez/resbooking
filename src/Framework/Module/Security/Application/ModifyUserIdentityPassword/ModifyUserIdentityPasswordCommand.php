<?php

declare(strict_types=1);

namespace Framework\Security\Application\ModifyUserIdentityPassword;

final readonly class ModifyUserIdentityPasswordCommand
{
    public function __construct(
        public string $token,
        public string $currentPassword,
        public string $newPassword,
    ) {
    }
}
