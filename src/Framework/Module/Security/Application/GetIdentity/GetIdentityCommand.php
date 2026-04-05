<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\GetIdentity;

final readonly class GetIdentityCommand
{
    public function __construct(
        public ?string $token,
    ) {
    }
}
