<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Projects\Entities\Place;
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ValueObjects\{Settings, User};
use App\Domain\Shared\{Capacity, Email, Phone};
use Faker\Generator as Faker;

class ProjectBuilder
{
    /**
     * @var array<Place> $places
     */
    private array $places;

    /**
     * @var array<User> $users
     */
    private array $users;

    public function __construct(private readonly Faker $faker)
    {
        $this->places = [];
        $this->users = [];
    }

    public function build(): Project
    {
        $settings = new Settings(
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(8),
            minNumberOfDiners: new Capacity(1),
            numberOfTables: new Capacity(25),
            phone: new Phone($this->faker->phoneNumber)
        );
        return Project::build(
            id: $this->faker->uuid,
            settings: $settings,
            places: $this->places ?? [],
            users: $this->users ?? [],
        );
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
