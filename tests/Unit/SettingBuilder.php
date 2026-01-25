<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Faker\Generator as Faker;

final class SettingBuilder
{
    private Email $email;
    private bool $hasReminders;
    private string $name;
    private Capacity $maxNumberOfDiners;
    private Capacity $minNumberOfDiners;
    private Capacity $numberOfTables;
    private Phone $phone;

    public function __construct(private Faker $faker)
    {
        $this->email = new Email($this->faker->email);
        $this->hasReminders = $this->faker->boolean;
        $this->name = $this->faker->name;
        $this->maxNumberOfDiners = new Capacity($this->faker->numberBetween(2, 12));
        $this->minNumberOfDiners = new Capacity($this->faker->numberBetween(2, 4));
        $this->numberOfTables = new Capacity($this->faker->numberBetween(25, 50));
        $this->phone = new Phone($this->faker->phoneNumber);
    }

    public function with(
        ?string $email = null,
        ?bool $hasReminders = null,
        ?string $name = null,
        ?int $maxNumberOfDiners = null,
        ?int $minNumberOfDiners = null,
        ?int $numberOfTables = null,
        ?string $phone = null,
    ): SettingBuilder {
        if ($email !== null) {
            $this->email = new Email($email);
        }
        if ($hasReminders !== null) {
            $this->hasReminders = $hasReminders;
        }
        if ($name !== null) {
            $this->name = $name;
        }
        if ($maxNumberOfDiners !== null) {
            $this->maxNumberOfDiners = new Capacity($maxNumberOfDiners);
        }
        if ($minNumberOfDiners !== null) {
            $this->minNumberOfDiners = new Capacity($minNumberOfDiners);
        }
        if ($numberOfTables !== null) {
            $this->numberOfTables = new Capacity($numberOfTables);
        }
        if ($phone !== null) {
            $this->phone = new Phone($phone);
        }
        return $this;
    }

    public function build(): Settings
    {
        return new Settings(
            email: $this->email,
            hasReminders: $this->hasReminders,
            name: $this->name,
            maxNumberOfDiners: $this->maxNumberOfDiners,
            minNumberOfDiners: $this->minNumberOfDiners,
            numberOfTables: $this->numberOfTables,
            phone: $this->phone,
        );
    }
}
