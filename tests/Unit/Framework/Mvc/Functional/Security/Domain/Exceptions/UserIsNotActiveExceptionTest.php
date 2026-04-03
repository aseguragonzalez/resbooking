<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Domain\Exceptions;

use Framework\Mvc\Security\Domain\Exceptions\UserIsNotActiveException;
use PHPUnit\Framework\TestCase;

final class UserIsNotActiveExceptionTest extends TestCase
{
    public function testExceptionHasExpectedMessageWithUsername(): void
    {
        $exception = new UserIsNotActiveException('inactive@example.com');

        $this->assertSame('User is not active: inactive@example.com.', $exception->getMessage());
    }

    public function testExceptionHasExpectedMessageWhenUsernameIsEmpty(): void
    {
        $exception = new UserIsNotActiveException();

        $this->assertSame('User is not active: .', $exception->getMessage());
    }
}
