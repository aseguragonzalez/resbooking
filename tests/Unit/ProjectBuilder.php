<?php

declare(strict_types=1);

namespace Tests\Unit;

use Faker\Factory as FakerFactory;
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\ValueObjects\Settings;
use App\Domain\Shared\{Capacity, Email, Phone};

class ProjectBuilder
{
    public function __construct(private $faker)
    {
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
        return Project::stored(id: $this->faker->uuid, settings: $settings);
    }
}
