<?php

declare(strict_types=1);

namespace Framework\Mvc\Security\Application\ActivateUserIdentity;

final readonly class ActivateUserIdentityCommand
{
    public function __construct(
        public string $token,
    ) {
    }
}
