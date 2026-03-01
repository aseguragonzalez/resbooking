<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateSettings;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use SeedWork\Application\Command;

final readonly class UpdateSettingsHandler implements UpdateSettings
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    /**
     * @param UpdateSettingsCommand $command
     */
    public function handle(Command $command): void
    {
        $settings = new Settings(
            email: new Email($command->email),
            hasReminders: $command->hasReminders,
            name: $command->name,
            maxNumberOfDiners: new Capacity($command->maxNumberOfDiners),
            minNumberOfDiners: new Capacity($command->minNumberOfDiners),
            numberOfTables: new Capacity($command->numberOfTables),
            phone: new Phone($command->phone)
        );
        $restaurantId = RestaurantId::fromString($command->restaurantId);
        $restaurant = $this->restaurantObtainer->obtain(id: $restaurantId)->updateSettings(settings: $settings);
        $this->restaurantRepository->save($restaurant);
    }
}
