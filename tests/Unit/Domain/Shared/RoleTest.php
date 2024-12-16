<?php

declare(strict_types=1);

use App\Domain\Shared\Role;
use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();

        Role::initialize();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testShouldRetrieveRoleById(): void
    {
        $id = $this->faker->numberBetween(1, 2);

        $role = Role::byId($id);

        $this->assertSame($id, $role->id);
    }

    public function testShouldRaiseExceptionWhenRetrieveRoleByIdWithInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Role::byId(0);
    }

    public function testShouldRetrieveRoleByName(): void
    {
        $name = $this->faker->randomElement([
            'admin',
            'host',
        ]);

        $role = Role::byName($name);

        $this->assertSame($name, $role->name);
    }
}
