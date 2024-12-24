<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
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
