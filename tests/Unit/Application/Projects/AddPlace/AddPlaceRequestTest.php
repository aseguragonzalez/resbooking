<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddPlace;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class AddPlaceRequestTest extends TestCase
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

    public function testAddPlaceShouldFailWhenNameIsInvalid(): void
    {
        $this->markTestIncomplete(AddPlaceRequestTest::NOT_IMPLEMENTED);
    }

    public function testAddPlaceShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->markTestIncomplete(AddPlaceRequestTest::NOT_IMPLEMENTED);
    }

    public function testAddPlaceShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete(AddPlaceRequestTest::NOT_IMPLEMENTED);
    }
}
