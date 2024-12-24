<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObjects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

use App\Domain\Shared\Turn;
use App\Domain\Shared\ValueObjects\OpenCloseEvent;

final class OpenCloseEventTest extends TestCase
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

    public function testEqualsShouldBeTrueWhenTurnAndDateAreEquals(): void
    {
        $date = new \DateTimeImmutable();
        $isAvailable = $this->faker->boolean();
        $turn = Turn::getByStartTime('13:30:00');

        $openCloseEvent = new OpenCloseEvent($date, $isAvailable, $turn);
        $other = new OpenCloseEvent($date, $isAvailable, $turn);

        $this->assertTrue($openCloseEvent->equals($other));
    }

    public function testEqualsShouldBeFalseWhenTurnIsDifferent(): void
    {
        $date = new \DateTimeImmutable();
        $isAvailable = $this->faker->boolean();
        $turn = Turn::getByStartTime('12:30:00');
        $otherTurn = Turn::getByStartTime('13:00:00');

        $openCloseEvent = new OpenCloseEvent($date, $isAvailable, $turn);
        $other = new OpenCloseEvent($date, $isAvailable, $otherTurn);

        $this->assertFalse($openCloseEvent->equals($other));
    }

    public function testEqualsShouldBeFalseWhenDateIsDifferent(): void
    {
        $date = new \DateTimeImmutable();
        $isAvailable = $this->faker->boolean();
        $turn = Turn::getByStartTime('14:30:00');
        $openCloseEvent = new OpenCloseEvent($date, $isAvailable, $turn);
        $other = new OpenCloseEvent($date->add(new \DateInterval('P1D')), $isAvailable, $turn);

        $this->assertFalse($openCloseEvent->equals($other));
    }
}
