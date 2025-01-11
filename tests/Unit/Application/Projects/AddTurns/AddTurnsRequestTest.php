<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddTurns;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class AddTurnsRequestTest extends TestCase
{
    public const NOT_IMPLEMENTED = 'Not implemented yet.';

    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testAddTurnsShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete(AddTurnsRequestTest::NOT_IMPLEMENTED);
    }

    public function testAddTurnsShouldFailWhenDayOfWeekDoesNotExist(): void
    {
        $this->markTestIncomplete(AddTurnsRequestTest::NOT_IMPLEMENTED);
    }

    public function testAddTurnsShouldFailWhenTurnDoesNotExist(): void
    {
        $this->markTestIncomplete(AddTurnsRequestTest::NOT_IMPLEMENTED);
    }

    public function testAddTurnsShouldDoNothingWhenTurnsIsInvalid(): void
    {
        $this->markTestIncomplete(AddTurnsRequestTest::NOT_IMPLEMENTED);
    }
}
