<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Restaurants;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Availability as AvailabilityModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\DiningArea as DiningAreaModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Restaurant as RestaurantModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Settings as SettingsModel;

final class RestaurantsMapper
{
    public static function mapToDomain(RestaurantModel $restaurantModel): Restaurant
    {
        return Restaurant::build(
            id: $restaurantModel->id,
            settings: self::mapSettingsToDomain($restaurantModel->settings),
            diningAreas: self::mapDiningAreasToDomain($restaurantModel->diningAreas),
            availabilities: self::mapAvailabilitiesToDomain($restaurantModel->availabilities),
            users: array_map(fn ($username) => new User(new Email($username)), $restaurantModel->users),
        );
    }

    private static function mapSettingsToDomain(SettingsModel $settingsModel): Settings
    {
        return new Settings(
            email: new Email($settingsModel->email),
            hasReminders: $settingsModel->hasReminders,
            name: $settingsModel->name,
            maxNumberOfDiners: new Capacity($settingsModel->maxNumberOfDiners),
            minNumberOfDiners: new Capacity($settingsModel->minNumberOfDiners),
            numberOfTables: new Capacity($settingsModel->numberOfTables),
            phone: new Phone($settingsModel->phone),
        );
    }

    /**
     * @param array<DiningAreaModel> $diningAreaModels
     * @return array<\Domain\Restaurants\Entities\DiningArea>
     */
    private static function mapDiningAreasToDomain(array $diningAreaModels): array
    {
        return array_map(
            fn (DiningAreaModel $diningAreaModel) => DiningArea::build(
                id: $diningAreaModel->id,
                capacity: new Capacity($diningAreaModel->capacity),
                name: $diningAreaModel->name,
            ),
            $diningAreaModels
        );
    }

    /**
     * @param array<AvailabilityModel> $availabilityModels
     * @return array<\Domain\Restaurants\ValueObjects\Availability>
     */
    private static function mapAvailabilitiesToDomain(array $availabilityModels): array
    {
        return array_map(
            fn (AvailabilityModel $availabilityModel) => new Availability(
                capacity: new Capacity($availabilityModel->capacity),
                dayOfWeek: DayOfWeek::getById($availabilityModel->dayOfWeekId),
                timeSlot: TimeSlot::getById($availabilityModel->timeSlotId),
            ),
            $availabilityModels
        );
    }

    public static function mapToModel(Restaurant $restaurant): RestaurantModel
    {
        return new RestaurantModel(
            id: $restaurant->getId()->value,
            settings: self::mapSettingsToModel($restaurant->getSettings()),
            diningAreas: self::mapDiningAreasToModel($restaurant->getDiningAreas()),
            availabilities: self::mapAvailabilitiesToModel($restaurant->getAvailabilities()),
            users: array_map(fn ($user) => $user->username->value, $restaurant->getUsers()),
        );
    }

    private static function mapSettingsToModel(Settings $settings): SettingsModel
    {
        return new SettingsModel(
            email: $settings->email->value,
            hasReminders: $settings->hasReminders,
            name: $settings->name,
            maxNumberOfDiners: $settings->maxNumberOfDiners->value,
            minNumberOfDiners: $settings->minNumberOfDiners->value,
            numberOfTables: $settings->numberOfTables->value,
            phone: $settings->phone->value,
        );
    }

    /**
     * @param array<\Domain\Restaurants\Entities\DiningArea> $diningAreas
     * @return array<\Infrastructure\Adapters\Repositories\Restaurants\Models\DiningArea>
     */
    private static function mapDiningAreasToModel(array $diningAreas): array
    {
        return array_map(
            fn ($diningArea) => new DiningAreaModel(
                id: $diningArea->id->value,
                capacity: $diningArea->capacity->value,
                name: $diningArea->name,
            ),
            $diningAreas
        );
    }

    /**
     * @param array<\Domain\Restaurants\ValueObjects\Availability> $availabilities
     * @return array<\Infrastructure\Adapters\Repositories\Restaurants\Models\Availability>
     */
    private static function mapAvailabilitiesToModel(array $availabilities): array
    {
        return array_map(
            fn ($availability) => new AvailabilityModel(
                capacity: $availability->capacity->value,
                dayOfWeekId: $availability->dayOfWeek->value,
                timeSlotId: $availability->timeSlot->value,
            ),
            $availabilities
        );
    }
}
