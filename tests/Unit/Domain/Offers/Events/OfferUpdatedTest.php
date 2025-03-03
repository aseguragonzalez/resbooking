<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Events;

use App\Domain\Offers\Entities\Offer;
use App\Domain\Offers\Events\OfferUpdated;
use App\Domain\Offers\ValueObjects\{Project, Settings};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class OfferUpdatedTest extends TestCase
{
    private Faker $faker;
    private Offer $offer;
    private string $offerId;

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
    }

    public function testCreateNewEvent(): void
    {
        $event = OfferUpdated::new(offerId: $this->offerId, offer: $this->offer);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('OfferUpdated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($this->offerId, $payload['offerId']);
        $this->assertSame($this->offer, $payload['offer']);
    }

    public function testBuildStoredEvent(): void
    {
        $event = OfferUpdated::build(offerId: $this->offerId, offer: $this->offer, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('OfferUpdated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($this->offerId, $payload['offerId']);
        $this->assertSame($this->offer, $payload['offer']);
    }
}
