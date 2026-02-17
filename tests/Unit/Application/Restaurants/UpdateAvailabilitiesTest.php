<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\UpdateAvailabilities;

use Application\Restaurants\UpdateAvailabilities\Availability;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesCommand;
use Application\Restaurants\UpdateAvailabilities\UpdateAvailabilitiesHandler;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;
use Seedwork\Domain\EntityId;

final class UpdateAvailabilitiesTest extends TestCase
{
    public function testExecuteUpdatesAvailabilities(): void
    {
        $restaurantIdString = 'test-restaurant-id';
        $restaurant = Restaurant::new('test@example.com', 'test-restaurant-id');
        $repository = $this->createMock(RestaurantRepository::class);
        $restaurantObtainer = $this->createMock(RestaurantObtainer::class);
        $restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with(EntityId::fromString($restaurantIdString))
            ->willReturn($restaurant);

        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Restaurant $savedRestaurant) use ($restaurant) {
                return $savedRestaurant === $restaurant;
            }));

        $command = new UpdateAvailabilitiesCommand(
            restaurantId: $restaurantIdString,
            availabilities: [
                new Availability(
                    dayOfWeekId: DayOfWeek::Monday->value,
                    timeSlotId: TimeSlot::H1200->value,
                    capacity: 15,
                ),
                new Availability(
                    dayOfWeekId: DayOfWeek::Tuesday->value,
                    timeSlotId: TimeSlot::H1230->value,
                    capacity: 20,
                ),
            ],
        );
        $service = new UpdateAvailabilitiesHandler($restaurantObtainer, $repository);

        $service->execute($command);

        $availabilities = $restaurant->getAvailabilities();
        $this->assertCount(2, $availabilities);
        $this->assertSame(15, $availabilities[0]->capacity->value);
        $this->assertSame(DayOfWeek::Monday, $availabilities[0]->dayOfWeek);
        $this->assertSame(TimeSlot::H1200, $availabilities[0]->timeSlot);
        $this->assertSame(20, $availabilities[1]->capacity->value);
        $this->assertSame(DayOfWeek::Tuesday, $availabilities[1]->dayOfWeek);
        $this->assertSame(TimeSlot::H1230, $availabilities[1]->timeSlot);
    }
}
