<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Domain\Exceptions;

use Framework\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use PHPUnit\Framework\TestCase;

final class SessionExpiredExceptionTest extends TestCase
{
    public function testExceptionHasExpectedMessage(): void
    {
        $exception = new SessionExpiredException();

        $this->assertSame('Session has expired.', $exception->getMessage());
    }
}
