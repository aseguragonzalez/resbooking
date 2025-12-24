<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Restaurants;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Restaurant as RestaurantModel;

final class InFileRestaurantRepository implements RestaurantRepository
{
    /**
     * @var array<string, Restaurant>
     */
    private array $restaurants = [];

    public function __construct(
        private readonly string $filePath = __DIR__ . '/inmemory_restaurants.json',
        private readonly RestaurantsMapper $mapper = new RestaurantsMapper(),
    ) {
        $file = $this->filePath;
        if (!file_exists($file)) {
            return;
        }

        $fileContent = file_get_contents($file);
        if ($fileContent === false) {
            return;
        }

        $data = json_decode($fileContent, true);
        if (!is_array($data)) {
            return;
        }

        /** @var array<string, mixed> $restaurantData */
        foreach ($data as $restaurantData) {
            $restaurantModel = RestaurantModel::fromArray((array) $restaurantData);
            $restaurant = $this->mapper->mapToDomain($restaurantModel);
            $this->restaurants[$restaurant->getId()] = $restaurant;
        }
    }

    public function __destruct()
    {
        $models = array_map(fn (Restaurant $restaurant) => $this->mapper->mapToModel($restaurant), $this->restaurants);
        file_put_contents($this->filePath, json_encode($models, JSON_PRETTY_PRINT));
    }

    /**
     * @param Restaurant $aggregateRoot
     */
    public function save($aggregateRoot): void
    {
        $this->restaurants[$aggregateRoot->getId()] = $aggregateRoot;
    }

    public function getById(string $id): Restaurant
    {
        return $this->restaurants[$id];
    }

    /**
     * @return array<Restaurant>
     */
    public function findByUserEmail(string $email): array
    {
        $matchingRestaurants = [];
        foreach ($this->restaurants as $restaurant) {
            $users = $restaurant->getUsers();
            foreach ($users as $user) {
                if ($user->username->value === $email) {
                    $matchingRestaurants[] = $restaurant;
                    break;
                }
            }
        }
        return $matchingRestaurants;
    }
}
