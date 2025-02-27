<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use App\Domain\Shared\Role;
use App\Domain\Users\Events\RoleRemovedFromUser;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class RoleRemovedFromUserTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $username = $this->faker->email;
        /** @var Role $role */
        $role = $this->faker->randomElement([Role::Admin, Role::User]);

        $event = RoleRemovedFromUser::new(username: $username, role: $role);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('RoleRemovedFromUser', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($username, $payload['username']);
        $this->assertEquals($role, $payload['role']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $username = $this->faker->email;
        /** @var Role $role */
        $role = $this->faker->randomElement([Role::Admin, Role::User]);

        $event = RoleRemovedFromUser::build(username: $username, role: $role, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('RoleRemovedFromUser', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($username, $payload['username']);
        $this->assertEquals($role, $payload['role']);
    }
}
