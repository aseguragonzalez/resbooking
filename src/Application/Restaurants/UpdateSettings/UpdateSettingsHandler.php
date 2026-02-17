<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateSettings;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Seedwork\Domain\EntityId;

final readonly class UpdateSettingsHandler implements UpdateSettings
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    public function execute(UpdateSettingsCommand $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: EntityId::fromString($command->restaurantId));
        $restaurant->updateSettings(new Settings(
            email: new Email($command->email),
            hasReminders: $command->hasReminders,
            name: $command->name,
            maxNumberOfDiners: new Capacity($command->maxNumberOfDiners),
            minNumberOfDiners: new Capacity($command->minNumberOfDiners),
            numberOfTables: new Capacity($command->numberOfTables),
            phone: new Phone($command->phone)
        ));
        $this->restaurantRepository->save($restaurant);
    }
}
