<?php

declare(strict_types=1);

namespace Framework\Web;

/**
 * Web authentication wiring for the MVC stack: sign-in redirect URL and session cookie name.
 *
 * Registered by the composition root (e.g. dashboard bootstrap) and consumed by
 * {@see \Framework\Web\Middlewares\Authentication}, {@see \Framework\Web\Middlewares\Authorization},
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
