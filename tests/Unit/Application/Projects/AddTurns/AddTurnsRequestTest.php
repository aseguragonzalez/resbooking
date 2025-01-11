<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddTurns;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class AddTurnsRequestRequestTest extends TestCase
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

    public function testAddTurnsShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenDayOfWeekDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldFailWhenTurnDoesNotExist(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnsShouldDoNothingWhenTurnsIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
