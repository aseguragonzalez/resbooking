<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\RefreshSignInSession;

final readonly class RefreshSignInSessionCommand
{
    public function __construct(
        public string $token,
    ) {
    }
}
