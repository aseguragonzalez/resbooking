<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\ModifyPlace;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class ModifyPlaceRequestTest extends TestCase
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

    public function testModifyPlaceShouldFailWhenCapacityIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testModifyPlaceShouldFailWhenNameIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
