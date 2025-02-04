<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Shared\Role;

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

    public function testGetByIdShouldRetrieveRoleById(): void
    {
        $id = $this->faker->numberBetween(1, 2);

        $role = Role::getById($id);

        $this->assertSame($id, $role->value);
    }

    public function testGetByIdShouldFailWhenRetrieveWithInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Role::getById(0);
    }

    public function testGeByNameShouldRetrieveRoleByName(): void
    {
        $name = $this->faker->randomElement([
            'admin',
            'user',
        ]);

        $role = Role::getByName($name);

        $this->assertSame($name, strtolower($role->name));
    }

    public function testGeByNameShouldFailWhenRetrieveWithInvalidName(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Role::getByName('invalid');
    }
}
