<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddOpenCloseEvent;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class AddOpenCloseEventRequestTest extends TestCase
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

    public function testFake(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
