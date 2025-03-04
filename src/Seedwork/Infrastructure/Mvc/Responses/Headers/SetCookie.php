<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class SetCookie extends Header
{
    public function __construct(
        private string $cookieName,
        private string $cookieValue,
        private int $expires = 0,
        private string $path = '/',
        private string $domain = '',
        private bool $secure = false,
        private bool $httpOnly = false,
        private string $sameSite = 'Lax'
    ) {
        parent::__construct('Set-Cookie', $this->buildValue());
    }

    private function buildValue(): string
    {
        $value = urlencode($this->cookieName) . '=' . urlencode($this->cookieValue);

        if ($this->expires > 0) {
            $value .= '; Expires=' . gmdate('D, d-M-Y H:i:s T', $this->expires);
        }

        if ($this->path !== '') {
            $value .= '; Path=' . $this->path;
        }

        if ($this->domain !== '') {
            $value .= '; Domain=' . $this->domain;
        }

        if ($this->secure) {
            $value .= '; Secure';
        }

        if ($this->httpOnly) {
            $value .= '; HttpOnly';
        }

        if ($this->sameSite !== '') {
            $value .= '; SameSite=' . $this->sameSite;
        }

        return $value;
    }
}
