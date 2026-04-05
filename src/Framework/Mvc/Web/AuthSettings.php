<?php

declare(strict_types=1);

namespace Framework\Mvc;

/**
 * Web authentication wiring for the MVC stack: sign-in redirect URL and session cookie name.
 *
 * Registered by the composition root (e.g. dashboard bootstrap) and consumed by
 * {@see \Framework\Mvc\Middlewares\Authentication}, {@see \Framework\Mvc\Middlewares\Authorization},
 * and account controllers.
 */
final readonly class AuthSettings
{
    public function __construct(
        public string $signInPath,
        public string $cookieName = 'auth',
    ) {
    }
}
