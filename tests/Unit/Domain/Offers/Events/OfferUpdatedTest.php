<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Offers\Entities\Offer;
use App\Domain\Offers\Events\OfferUpdated;
use App\Domain\Offers\ValueObjects\{Project, Settings};

final class OfferUpdatedTest extends TestCase
{
    private $faker = null;
    private $offer = null;
    private $offerId = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $project = new Project($this->faker->uuid);
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->add(new \DateInterval('P10D'));
        $setting = new Settings(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $startDate,
            endDate: $endDate,
        );
        $this->offer = Offer::new(project: $project, settings: $setting);
        $this->offerId = $this->offer->getId();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->offer = null;
        $this->offerId = null;
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $event = OfferUpdated::new(offerId: $this->offerId, offer: $this->offer);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('OfferUpdated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($this->offerId, $payload['offerId']);
        $this->assertEquals($this->offer, $payload['offer']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $event = OfferUpdated::build(offerId: $this->offerId, offer: $this->offer, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('OfferUpdated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($this->offerId, $payload['offerId']);
        $this->assertEquals($this->offer, $payload['offer']);
    }
}
