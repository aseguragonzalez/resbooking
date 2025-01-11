<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\RemoveTurns;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class RemoveTurnsRequestTest extends TestCase
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

    public function testRemoveTurnsShouldDoNothingWhenTurnsIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
