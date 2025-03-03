<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use App\Domain\Shared\Role;
use App\Domain\Users\Events\RoleAddedToUser;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class RoleAddedToUserTest extends TestCase
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
        $username = $this->faker->email;
        /** @var Role $role */
        $role = $this->faker->randomElement([Role::Admin, Role::User]);

        $event = RoleAddedToUser::new(username: $username, role: $role);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('RoleAddedToUser', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($role, $payload['role']);
    }

    public function testBuildStoredEvent(): void
    {
        $username = $this->faker->email;
        /** @var Role $role */
        $role = $this->faker->randomElement([Role::Admin, Role::User]);

        $event = RoleAddedToUser::build(username: $username, role: $role, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('RoleAddedToUser', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($role, $payload['role']);
    }
}
