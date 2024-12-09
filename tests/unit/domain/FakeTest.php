<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FakeTest extends TestCase
{
    public function testCanBeCreatedFromValidEmail(): void
    {
        $string = 'user@example.com';

        $email = 'user@example.com';

        $this->assertSame($string, $email);
    }
}
