<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\Role;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
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
        /** @var string $name */
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
