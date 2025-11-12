<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Projects\Entities\Place;
use Domain\Projects\Entities\Project;
use Domain\Projects\ValueObjects\Settings;
use Domain\Projects\ValueObjects\User;
use Domain\Shared\ValueObjects\OpenCloseEvent;
use Domain\Shared\ValueObjects\TurnAvailability;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Faker\Generator as Faker;

final class ProjectBuilder
{
    /**
     * @var array<Place> $places
     */
    private array $places;

    /**
     * @var array<User> $users
     */
    private array $users;

    /**
     * @var array<TurnAvailability> $turns
     */
    private array $turns;

    /**
     * @var array<OpenCloseEvent> $openCloseEvents
     */
    private array $openCloseEvents;

    private ?Settings $settings;

    public function __construct(private readonly Faker $faker)
    {
        $this->openCloseEvents = [];
        $this->places = [];
        $this->turns = [];
        $this->users = [];
        $this->settings = null;
    }

    public function build(): Project
    {
        $settings = new Settings(
            email: new Email($this->faker->email),
            hasReminders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(8),
            minNumberOfDiners: new Capacity(1),
            numberOfTables: new Capacity(25),
            phone: new Phone($this->faker->phoneNumber)
        );
        return Project::build(
            id: $this->faker->uuid,
            settings: $this->settings ?? $settings,
            places: $this->places,
            users: $this->users,
            turns: $this->turns,
            openCloseEvents: $this->openCloseEvents,
        );
    }

    /**
     * @param array<OpenCloseEvent> $openCloseEvents
     * @return ProjectBuilder
     */
    public function withOpenCloseEvents(array $openCloseEvents = []): ProjectBuilder
    {
        $this->openCloseEvents = $openCloseEvents;
        return $this;
    }

    /**
     * @param array<Place> $places
     * @return ProjectBuilder
     */
    public function withPlaces(array $places = []): ProjectBuilder
    {
        $this->places = $places;
        return $this;
    }

    public function withSettings(Settings $settings): ProjectBuilder
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param array<TurnAvailability> $turns
     * @return ProjectBuilder
     */
    public function withTurns(array $turns = []): ProjectBuilder
    {
        $this->turns = $turns;
        return $this;
    }

    /**
     * @param array<User> $users
     * @return ProjectBuilder
     */
    public function withUsers(array $users = []): ProjectBuilder
    {
        $this->users = $users;
        return $this;
    }
}
