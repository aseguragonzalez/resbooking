<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Restaurants\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;

final readonly class SelectRestaurant extends FormModel
{
    /**
     * @param array<object{id: string, name: string}> $restaurants
     * @param array<string, string> $errors
     */
    private function __construct(
        array $errors = [],
        public array $restaurants = [],
        public string $backUrl = '/',
        public bool $hasNoRestaurants = false,
    ) {
        parent::__construct(pageTitle: '{{restaurants.select.title}}', errors: $errors);
    }

    /**
     * @param array<object{id: string, name: string}> $restaurants
     */
    public static function withRestaurants(array $restaurants, string $backUrl): self
    {
        return new self(restaurants: $restaurants, backUrl: $backUrl);
    }

    public static function withNoRestaurants(string $backUrl): self
    {
        return new self(backUrl: $backUrl, hasNoRestaurants: true);
    }

    /**
     * @param array<string, string> $errors
     */
    public static function withErrors(array $errors, string $backUrl): self
    {
        return new self(errors: $errors, backUrl: $backUrl);
    }
}
