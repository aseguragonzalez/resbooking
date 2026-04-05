<?php

declare(strict_types=1);

namespace Framework\Module\Security\Application\SignOut;

final readonly class SignOutCommand
{
    public function __construct(
        public string $token,
    ) {
    }
}
