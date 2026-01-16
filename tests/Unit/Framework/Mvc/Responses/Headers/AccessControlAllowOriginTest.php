<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Framework\Mvc\Responses\Headers\AccessControlAllowOrigin;

final class AccessControlAllowOriginTest extends TestCase
{
    public function testAnyReturnsHeaderWithWildcard(): void
    {
        $header = AccessControlAllowOrigin::any();

        $this->assertSame('Access-Control-Allow-Origin', $header->name);
        $this->assertSame('*', $header->value);
    }

    public function testNoneReturnsHeaderWithNull(): void
    {
        $header = AccessControlAllowOrigin::none();

        $this->assertSame('Access-Control-Allow-Origin', $header->name);
        $this->assertSame('null', $header->value);
    }

    public function testSpecificReturnsHeaderWithGivenOrigin(): void
    {
        $origin = 'https://specific.com';
        $header = AccessControlAllowOrigin::specific($origin);

        $this->assertSame('Access-Control-Allow-Origin', $header->name);
        $this->assertSame($origin, $header->value);
    }

    public function testToStringReturnsFormattedHeader(): void
    {
        $header = AccessControlAllowOrigin::specific('https://example.com');

        $this->assertSame('Access-Control-Allow-Origin: https://example.com', (string) $header);
    }
}
