<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Offers;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Offers\OfferRepository;

final class EnableOfferTest extends TestCase
{
    private $faker = null;
    private OfferRepository $offerRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->offerRepository = $this->createMock(OfferRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->offerRepository = null;
    }

    public function testFake(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
