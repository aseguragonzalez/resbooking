<?php

declare(strict_types=1);

namespace Framework\Security\Application\ActivateUserIdentity;

final readonly class ActivateUserIdentityCommand
{
    public function __construct(
        public string $token,
    ) {
    }
}
