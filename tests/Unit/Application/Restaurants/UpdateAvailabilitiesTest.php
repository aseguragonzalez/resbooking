<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\UpdateAvailabilities;

use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesCommand;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesHandler;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use PHPUnit\Framework\TestCase;

final class UpdateAvailabilitiesTest extends TestCase
{
    public function testHandleUpdatesAvailabilities(): void
    {
        $restaurantIdString = 'test-restaurant-id';
        $restaurant = Restaurant::create('test@example.com', 'test-restaurant-id');
        $repository = $this->createMock(RestaurantRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with(RestaurantId::fromString($restaurantIdString))
            ->willReturn($restaurant);

        $savedRestaurant = null;
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Restaurant $r) use (&$savedRestaurant) {
                $savedRestaurant = $r;
                return true;
            }));

        $command = new UpdateAvailabilitiesCommand(
            restaurantId: $restaurantIdString,
            availabilities: [
                [
                    'dayOfWeekId' => DayOfWeek::Monday->value,
                    'timeSlotId' => TimeSlot::H1200->value,
                    'capacity' => 15,
                ],
                [
                    'dayOfWeekId' => DayOfWeek::Tuesday->value,
                    'timeSlotId' => TimeSlot::H1230->value,
                    'capacity' => 20,
                ],
            ],
        );
        $restaurantObtainer = new RestaurantObtainer($repository);
        $service = new UpdateAvailabilitiesHandler($restaurantObtainer, $repository);

        $service->handle($command);

        $this->assertInstanceOf(Restaurant::class, $savedRestaurant);
        $availabilities = $savedRestaurant->getAvailabilities();
        $this->assertCount(2, $availabilities);
        $this->assertSame(15, $availabilities[0]->capacity->value);
        $this->assertSame(DayOfWeek::Monday, $availabilities[0]->dayOfWeek);
        $this->assertSame(TimeSlot::H1200, $availabilities[0]->timeSlot);
        $this->assertSame(20, $availabilities[1]->capacity->value);
        $this->assertSame(DayOfWeek::Tuesday, $availabilities[1]->dayOfWeek);
        $this->assertSame(TimeSlot::H1230, $availabilities[1]->timeSlot);
    }
}
