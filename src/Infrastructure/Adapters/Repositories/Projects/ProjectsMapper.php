<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects;

use Domain\Projects\Entities\Place;
use Domain\Projects\Entities\Project;
use Domain\Projects\ValueObjects\Settings;
use Domain\Projects\ValueObjects\TurnAvailability;
use Domain\Projects\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\Turn;
use Infrastructure\Adapters\Repositories\Projects\Models\Place as PlaceModel;
use Infrastructure\Adapters\Repositories\Projects\Models\Project as ProjectModel;
use Infrastructure\Adapters\Repositories\Projects\Models\Settings as SettingsModel;
use Infrastructure\Adapters\Repositories\Projects\Models\TurnAvailability as TurnAvailabilityModel;

final class ProjectsMapper
{
    public static function mapToDomain(ProjectModel $projectModel): Project
    {
        return Project::build(
            id: $projectModel->id,
            settings: self::mapSettingsToDomain($projectModel->settings),
            places: self::mapPlacesToDomain($projectModel->places),
            turns: self::mapTurnsToDomain($projectModel->turnAvailabilities),
            users: array_map(fn ($username) => new User(new Email($username)), $projectModel->users),
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
     * @param array<PlaceModel> $placeModels
     * @return array<\Domain\Projects\Entities\Place>
     */
    private static function mapPlacesToDomain(array $placeModels): array
    {
        return array_map(
            fn (PlaceModel $placeModel) => Place::build(
                id: $placeModel->id,
                capacity: new Capacity($placeModel->capacity),
                name: $placeModel->name,
            ),
            $placeModels
        );
    }

    /**
     * @param array<TurnAvailabilityModel> $turnModels
     * @return array<\Domain\Projects\ValueObjects\TurnAvailability>
     */
    private static function mapTurnsToDomain(array $turnModels): array
    {
        return array_map(
            fn (TurnAvailabilityModel $turnModel) => new TurnAvailability(
                capacity: new Capacity($turnModel->capacity),
                dayOfWeek: DayOfWeek::from($turnModel->dayOfWeekId),
                turn: Turn::from($turnModel->turnId),
            ),
            $turnModels
        );
    }

    public static function mapToModel(Project $project): ProjectModel
    {
        return new ProjectModel(
            id: $project->getId(),
            settings: self::mapSettingsToModel($project->getSettings()),
            places: self::mapPlacesToModel($project->getPlaces()),
            turnAvailabilities: self::mapTurnsToModel($project->getTurns()),
            users: array_map(fn ($user) => $user->username->getValue(), $project->getUsers()),
        );
    }

    private static function mapSettingsToModel(Settings $settings): SettingsModel
    {
        return new SettingsModel(
            email: $settings->email->getValue(),
            hasReminders: $settings->hasReminders,
            name: $settings->name,
            maxNumberOfDiners: $settings->maxNumberOfDiners->value,
            minNumberOfDiners: $settings->minNumberOfDiners->value,
            numberOfTables: $settings->numberOfTables->value,
            phone: $settings->phone->getValue(),
        );
    }

    /**
     * @param array<\Domain\Projects\Entities\Place> $places
     * @return array<\Infrastructure\Adapters\Repositories\Projects\Models\Place>
     */
    private static function mapPlacesToModel(array $places): array
    {
        return array_map(
            fn ($place) => new PlaceModel(
                id: $place->getId(),
                capacity: $place->capacity->value,
                name: $place->name,
            ),
            $places
        );
    }

    /**
     * @param array<\Domain\Projects\ValueObjects\TurnAvailability> $turns
     * @return array<\Infrastructure\Adapters\Repositories\Projects\Models\TurnAvailability>
     */
    private static function mapTurnsToModel(array $turns): array
    {
        return array_map(
            fn ($turn) => new TurnAvailabilityModel(
                capacity: $turn->capacity->value,
                dayOfWeekId: $turn->dayOfWeek->value,
                turnId: $turn->turn->value,
            ),
            $turns
        );
    }
}
