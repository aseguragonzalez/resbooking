<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Shared\Role;
use App\Domain\Users\Events\RoleRemovedFromUser;

final class RoleRemovedFromUserTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $username = $this->faker->email;
        $role = $this->faker->randomElement([Role::ADMIN, Role::USER]);

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
        $role = $this->faker->randomElement([Role::ADMIN, Role::USER]);

        $event = RoleRemovedFromUser::build(username: $username, role: $role, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('RoleRemovedFromUser', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($username, $payload['username']);
        $this->assertEquals($role, $payload['role']);
    }
}
