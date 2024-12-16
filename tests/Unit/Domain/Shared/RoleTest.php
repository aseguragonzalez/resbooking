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
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testShouldRetrieveRoleById(): void
    {
        $id = $this->faker->numberBetween(1, 2);

        $role = Role::getById($id);

        $this->assertSame($id, $role->value);
    }

    public function testShouldRaiseExceptionWhenRetrieveRoleByIdWithInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Role::getById(0);
    }

    public function testShouldRetrieveRoleByName(): void
    {
        $name = $this->faker->randomElement([
            'admin',
            'host',
        ]);

        $role = Role::getByName($name);

        $this->assertSame($name, strtolower($role->name));
    }
}
