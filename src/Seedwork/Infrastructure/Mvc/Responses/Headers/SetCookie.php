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

    public static function removeCookie(string $cookieName): self
    {
        return new self($cookieName, '', 0);
    }

    public static function createSecureCookie(
        string $cookieName,
        string $cookieValue,
        int $expires = 0,
        string $path = '/',
    ): self {
        return new self(
            cookieName: $cookieName,
            cookieValue: $cookieValue,
            expires: $expires,
            path: $path,
            secure: true,
            httpOnly: true,
            sameSite: 'Strict'
        );
    }

    private function buildValue(): string
    {
        $value = urlencode($this->cookieName) . '=' . urlencode($this->cookieValue);

        if ($this->expires > 0) {
            $value .= '; Expires=' . gmdate('D, d-M-Y H:i:s T', $this->expires);
        } elseif ($this->expires === 0) {
            $value .= '; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Max-Age=0';
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
