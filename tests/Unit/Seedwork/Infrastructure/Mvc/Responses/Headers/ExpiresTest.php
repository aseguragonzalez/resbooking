<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\Expires;

final class ExpiresTest extends TestCase
{
    public function testExpiresHeader(): void
    {
        $date = new \DateTimeImmutable('2023-10-01 12:00:00');
        $expires = new Expires($date);

        $this->assertSame('Expires', $expires->name);
        $this->assertSame('Sun, 01 Oct 2023 12:00:00 GMT', $expires->value);
    }

    public function testExpiresHeaderWithDifferentDate(): void
    {
        $date = new \DateTimeImmutable('2023-12-25 18:30:00');
        $expires = new Expires($date);

        $this->assertSame('Expires', $expires->name);
        $this->assertSame('Mon, 25 Dec 2023 18:30:00 GMT', $expires->value);
    }
}
