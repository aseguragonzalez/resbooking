<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\UserCreated;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Email;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class UserCreatedTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testCreateNewEvent(): void
    {
        $restaurantId = $this->faker->uuid;
        $user = new User(username: new Email($this->faker->email));

        $event = UserCreated::new(restaurantId: $restaurantId, user: $user);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('UserCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($restaurantId, $payload['restaurantId']);
        $this->assertSame($user, $payload['user']);
    }
}
