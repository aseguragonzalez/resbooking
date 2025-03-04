<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Responses\Headers;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Responses\Headers\AccessControlAllowOrigin;

final class AccessControlAllowOriginTest extends TestCase
{
    public function testConstructorSetsNameAndValue(): void
    {
        $header = new AccessControlAllowOrigin('https://example.com');

        $this->assertSame('Access-Control-Allow-Origin', $header->name);
        $this->assertSame('https://example.com', $header->value);
    }

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
}
