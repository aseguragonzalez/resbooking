<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\SetCookie;

final class SetCookieTest extends TestCase
{
    public function testSetCookieWithRequiredParameters(): void
    {
        $setCookie = new SetCookie('test', 'value');
        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('test=value', $setCookie->value);
    }

    public function testSetCookieWithAllParameters(): void
    {
        $expires = time() + 3600;
        $setCookie = new SetCookie('test', 'value', $expires, '/path', 'example.com', true, true, 'Strict');
        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('test=value', $setCookie->value);
        $this->assertStringContainsString('Expires=' . gmdate('D, d-M-Y H:i:s T', $expires), $setCookie->value);
        $this->assertStringContainsString('Path=/path', $setCookie->value);
        $this->assertStringContainsString('Domain=example.com', $setCookie->value);
        $this->assertStringContainsString('Secure', $setCookie->value);
        $this->assertStringContainsString('HttpOnly', $setCookie->value);
        $this->assertStringContainsString('SameSite=Strict', $setCookie->value);
    }

    public function testSetCookieWithDefaultValues(): void
    {
        $setCookie = new SetCookie('test', 'value');
        $this->assertStringContainsString('Path=/', $setCookie->value);
        $this->assertStringContainsString('SameSite=Lax', $setCookie->value);
    }

    public function testSetCookieWithoutExpires(): void
    {
        $setCookie = new SetCookie('test', 'value', 0);
        $this->assertStringNotContainsString('Expires=', $setCookie->value);
    }

    public function testSetCookieWithoutDomain(): void
    {
        $setCookie = new SetCookie('test', 'value', 0, '/', '', true, true, 'Lax');
        $this->assertStringNotContainsString('Domain=', $setCookie->value);
    }

    public function testSetCookieWithoutSecure(): void
    {
        $setCookie = new SetCookie('test', 'value', 0, '/', '', false, true, 'Lax');
        $this->assertStringNotContainsString('Secure', $setCookie->value);
    }

    public function testSetCookieWithoutHttpOnly(): void
    {
        $setCookie = new SetCookie('test', 'value', 0, '/', '', true, false, 'Lax');
        $this->assertStringNotContainsString('HttpOnly', $setCookie->value);
    }

    public function testSetCookieWithoutSameSite(): void
    {
        $setCookie = new SetCookie('test', 'value', 0, '/', '', true, true, '');
        $this->assertStringNotContainsString('SameSite=', $setCookie->value);
    }
}
