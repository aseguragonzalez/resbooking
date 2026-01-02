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
        $setCookie = new SetCookie('test', 'value', -1);

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

    public function testSetCookieToString(): void
    {
        $expires = time() + 3600;
        $setCookie = new SetCookie('test', 'value', $expires, '/path', 'example.com', true, true, 'Strict');
        $expectedString = 'Set-Cookie: test=value; Expires='
            . gmdate('D, d-M-Y H:i:s T', $expires)
            . '; Path=/path; Domain=example.com; Secure; HttpOnly; SameSite=Strict';

        $this->assertSame($expectedString, (string) $setCookie);
    }

    public function testRemoveCookie(): void
    {
        $setCookie = SetCookie::removeCookie('testCookie');

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('testCookie=', $setCookie->value);
        $this->assertStringContainsString('Expires=Thu, 01 Jan 1970 00:00:00 GMT', $setCookie->value);
        $this->assertStringContainsString('Max-Age=0', $setCookie->value);
        $this->assertStringContainsString('Path=/', $setCookie->value);
    }

    public function testCreateSecureCookie(): void
    {
        $expires = time() + 3600;
        $setCookie = SetCookie::createSecureCookie('authToken', 'token123', $expires);

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('authToken=token123', $setCookie->value);
        $this->assertStringContainsString('Expires=' . gmdate('D, d-M-Y H:i:s T', $expires), $setCookie->value);
        $this->assertStringContainsString('Path=/', $setCookie->value);
        $this->assertStringContainsString('Secure', $setCookie->value);
        $this->assertStringContainsString('HttpOnly', $setCookie->value);
        $this->assertStringContainsString('SameSite=Strict', $setCookie->value);
    }

    public function testCreateSecureCookieWithCustomPath(): void
    {
        $setCookie = SetCookie::createSecureCookie('authToken', 'token123', 0, '/admin');

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('authToken=token123', $setCookie->value);
        $this->assertStringContainsString('Path=/admin', $setCookie->value);
        $this->assertStringContainsString('Secure', $setCookie->value);
        $this->assertStringContainsString('HttpOnly', $setCookie->value);
        $this->assertStringContainsString('SameSite=Strict', $setCookie->value);
    }

    public function testSetCookieWithUrlEncodedNameAndValue(): void
    {
        $setCookie = new SetCookie('cookie name', 'value=with=equals; and semicolons');

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('cookie+name=', $setCookie->value);
        $this->assertStringContainsString('value%3Dwith%3Dequals%3B+and+semicolons', $setCookie->value);
    }

    public function testSetCookieWithExpiresZero(): void
    {
        $setCookie = new SetCookie('test', 'value', 0);

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('test=value', $setCookie->value);
        $this->assertStringContainsString('Expires=Thu, 01 Jan 1970 00:00:00 GMT', $setCookie->value);
        $this->assertStringContainsString('Max-Age=0', $setCookie->value);
    }

    public function testSetCookieWithSameSiteNone(): void
    {
        $setCookie = new SetCookie('test', 'value', 0, '/', '', true, true, 'None');

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('test=value', $setCookie->value);
        $this->assertStringContainsString('SameSite=None', $setCookie->value);
    }

    public function testSetCookieWithSameSiteLax(): void
    {
        $setCookie = new SetCookie('test', 'value');

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('test=value', $setCookie->value);
        $this->assertStringContainsString('SameSite=Lax', $setCookie->value);
    }

    public function testSetCookieWithSameSiteStrict(): void
    {
        $setCookie = new SetCookie('test', 'value', 0, '/', '', false, false, 'Strict');

        $this->assertSame('Set-Cookie', $setCookie->name);
        $this->assertStringContainsString('test=value', $setCookie->value);
        $this->assertStringContainsString('SameSite=Strict', $setCookie->value);
    }
}
