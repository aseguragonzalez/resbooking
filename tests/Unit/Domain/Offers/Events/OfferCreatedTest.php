<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\Events;

use Domain\Offers\Entities\Offer;
use Domain\Offers\Events\OfferCreated;
use Domain\Offers\ValueObjects\Project;
use Domain\Offers\ValueObjects\Settings;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class OfferCreatedTest extends TestCase
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
        $event = OfferCreated::new(offerId: $this->offerId, offer: $this->offer);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('OfferCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($this->offerId, $payload['offerId']);
        $this->assertSame($this->offer, $payload['offer']);
    }

    public function testBuildStoredEvent(): void
    {
        $event = OfferCreated::build(offerId: $this->offerId, offer: $this->offer, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('OfferCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($this->offerId, $payload['offerId']);
        $this->assertSame($this->offer, $payload['offer']);
    }
}
