<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddPlace;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class AddPlaceRequestTest extends TestCase
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

    public function testAddPlaceShouldFailWhenNameIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
