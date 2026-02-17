<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Events;

use Domain\Restaurants\Events\UserRemoved;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Email;
use Faker\Factory as FakerFactory;
use Seedwork\Domain\EntityId;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class UserRemovedTest extends TestCase
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

        $event = UserRemoved::new(restaurantId: EntityId::fromString($restaurantId), user: $user);

        $this->assertNotEmpty($event->id->value);
        $this->assertSame('UserRemoved', $event->type);
        $this->assertSame('1.0', $event->version);
        $payload = $event->payload;
        $this->assertSame($restaurantId, $payload['restaurantId']);
        $this->assertSame($user, $payload['user']);
    }
}
