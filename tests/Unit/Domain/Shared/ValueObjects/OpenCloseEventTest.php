<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObjects;

use App\Domain\Shared\Turn;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class OpenCloseEventTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testEqualsWhenTurnAndDateAreEquals(): void
    {
        $date = new \DateTimeImmutable();
        $isAvailable = $this->faker->boolean();
        $turn = Turn::getByStartTime('13:30:00');

        $openCloseEvent = new OpenCloseEvent($date, $isAvailable, $turn);
        $other = new OpenCloseEvent($date, $isAvailable, $turn);

        $this->assertTrue($openCloseEvent->equals($other));
    }

    public function testEqualsWhenTurnIsDifferent(): void
    {
        $date = new \DateTimeImmutable();
        $isAvailable = $this->faker->boolean();
        $turn = Turn::getByStartTime('12:30:00');
        $otherTurn = Turn::getByStartTime('13:00:00');

        $openCloseEvent = new OpenCloseEvent($date, $isAvailable, $turn);
        $other = new OpenCloseEvent($date, $isAvailable, $otherTurn);

        $this->assertFalse($openCloseEvent->equals($other));
    }

    public function testEqualsWhenDateIsDifferent(): void
    {
        $date = new \DateTimeImmutable();
        $isAvailable = $this->faker->boolean();
        $turn = Turn::getByStartTime('14:30:00');
        $openCloseEvent = new OpenCloseEvent($date, $isAvailable, $turn);
        $other = new OpenCloseEvent($date->add(new \DateInterval('P1D')), $isAvailable, $turn);

        $this->assertFalse($openCloseEvent->equals($other));
    }
}
