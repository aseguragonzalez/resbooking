<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\UserCreated;
use Domain\Restaurants\ValueObjects\User;
use Domain\Restaurants\ValueObjects\RestaurantId;
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
        $restaurantId = RestaurantId::fromString($this->faker->uuid);
        $email = $this->faker->email;
        $user = new User(username: new Email($email));

        $event = UserCreated::create($restaurantId, $user);

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('user.created', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId->value, $payload['restaurant_id']);
        $this->assertSame($email, $payload['user_email']);
    }
}
