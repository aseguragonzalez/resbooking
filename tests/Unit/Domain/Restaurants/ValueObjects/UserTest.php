<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\ValueObjects;

use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\TimeSlot;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testCreateInstance(): void
    {
        $email = new Email('user@example.com');
        $user = new User($email);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($email, $user->username);
    }

    public function testEqualsReturnsTrueWhenSameUsername(): void
    {
        $email = new Email('user@example.com');
        $user1 = new User($email);
        $user2 = new User($email);

        $this->assertTrue($user1->equals($user2));
    }

    public function testEqualsReturnsTrueWhenDifferentEmailInstanceButSameValue(): void
    {
        $user1 = new User(new Email('user@example.com'));
        $user2 = new User(new Email('user@example.com'));

        $this->assertTrue($user1->equals($user2));
    }

    public function testEqualsReturnsFalseWhenDifferentUsername(): void
    {
        $user1 = new User(new Email('user1@example.com'));
        $user2 = new User(new Email('user2@example.com'));

        $this->assertFalse($user1->equals($user2));
    }

    public function testEqualsReturnsFalseWhenNotInstanceOfUser(): void
    {
        $user = new User(new Email('user@example.com'));
        $other = new Availability(
            capacity: new Capacity(100),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );

        $this->assertFalse($user->equals($other));
    }
}
