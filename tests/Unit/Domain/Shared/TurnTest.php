<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\Turn;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class TurnTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testGetByIdShouldRetrieveById(): void
    {
        $id = $this->faker->numberBetween(1, 24);

        $turn = Turn::getById($id);

        $this->assertSame($id, $turn->value);
    }

    public function testGetByIdShouldFailWhenRetrieveWithInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Turn::getById(0);
    }

    public function testGetByStartTimeShouldRetrieveByStartTime(): void
    {
        /** @var string $startTime */
        $startTime = $this->faker->randomElement([
            '12:00:00',
            '12:30:00',
            '13:00:00',
            '13:30:00',
            '14:00:00',
            '14:30:00',
            '15:00:00',
            '15:30:00',
            '16:00:00',
            '16:30:00',
            '17:00:00',
            '17:30:00',
            '18:00:00',
            '18:30:00',
            '19:00:00',
            '19:30:00',
            '20:00:00',
            '20:30:00',
            '21:00:00',
            '21:30:00',
            '22:00:00',
            '22:30:00',
            '23:00:00',
            '23:30:00',
        ]);

        $turn = Turn::getByStartTime($startTime);

        $this->assertSame($startTime, $turn->toString());
    }

    public function testGetByStartTimeShouldFailWhenRetrieveWithInvalidValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Turn::getByStartTime($this->faker->lexify('?????'));
    }
}
