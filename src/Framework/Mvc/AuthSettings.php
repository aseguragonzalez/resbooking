<?php

declare(strict_types=1);

namespace Framework\Mvc;

final readonly class AuthSettings
{
    public function __construct(
        public string $signInPath,
        public string $signOutPath,
        public string $cookieName = 'auth',
    ) {
    }
}
