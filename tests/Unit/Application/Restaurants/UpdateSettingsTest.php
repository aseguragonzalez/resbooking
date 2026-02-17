<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\UpdateSettings;

use Application\Restaurants\UpdateSettings\UpdateSettingsCommand;
use Application\Restaurants\UpdateSettings\UpdateSettingsHandler;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Seedwork\Domain\EntityId;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;
use Tests\Unit\RestaurantBuilder;

final class UpdateSettingsTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;
    private MockObject&RestaurantRepository $restaurantRepository;
    private MockObject&RestaurantObtainer $restaurantObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
        $this->restaurantObtainer = $this->createMock(RestaurantObtainer::class);
    }

    public function testUpdateRestaurantSettings(): void
    {
        $settings = new Settings(
            email: new Email($this->faker->email),
            hasReminders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(10),
            minNumberOfDiners: new Capacity(10),
            numberOfTables: new Capacity(10),
            phone: new Phone($this->faker->phoneNumber)
        );
        $restaurant = $this->restaurantBuilder->withSettings($settings)->build();
        $restaurantIdString = $this->faker->uuid;
        $this->restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with(EntityId::fromString($restaurantIdString))
            ->willReturn($restaurant);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('save')
            ->with($restaurant);
        $request = new UpdateSettingsCommand(
            restaurantId: $restaurantIdString,
            email: $this->faker->email,
            hasReminders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 10,
            numberOfTables: 10,
            phone: $this->faker->phoneNumber
        );
        $ApplicationService = new UpdateSettingsHandler($this->restaurantObtainer, $this->restaurantRepository);

        $ApplicationService->execute($request);

        $currentSettings = $restaurant->getSettings();
        $this->assertSame($request->email, $currentSettings->email->value);
        $this->assertSame($request->hasReminders, $currentSettings->hasReminders);
        $this->assertSame($request->name, $currentSettings->name);
        $this->assertSame($request->maxNumberOfDiners, $currentSettings->maxNumberOfDiners->value);
        $this->assertSame($request->minNumberOfDiners, $currentSettings->minNumberOfDiners->value);
        $this->assertSame($request->numberOfTables, $currentSettings->numberOfTables->value);
        $this->assertSame($request->phone, $currentSettings->phone->value);
    }
}
