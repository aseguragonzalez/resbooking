<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class OfferTest extends TestCase
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
}
