<?php

declare(strict_types=1);

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Offers\OfferRepository;

final class ModifyOfferTest extends TestCase
{
    private $faker = null;
    private OffersRepository $offerRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->offerRepository = $this->createMock(OffersRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->offerRepository = null;
    }

    public function testFake(): void
    {
        $this->assertTrue(false);
    }
}
