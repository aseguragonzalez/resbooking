<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\ActivateUserIdentity;

final readonly class ActivateUserIdentityCommand
{
    public function __construct(
        public string $token,
    ) {
    }
}
