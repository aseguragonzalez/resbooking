<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Restaurants;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Availability as AvailabilityModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\DiningArea as DiningAreaModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Restaurant as RestaurantModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Settings as SettingsModel;
use Seedwork\Application\Messaging\DomainEventsBus;
use PDO;

final readonly class SqlRestaurantRepository implements RestaurantRepository
{
    public function __construct(
        private PDO $db,
        private DomainEventsBus $domainEventsBus,
        private RestaurantsMapper $mapper = new RestaurantsMapper(),
    ) {
    }

    /**
     * @param Restaurant $aggregateRoot
     */
    public function save($aggregateRoot): void
    {
        $restaurantModel = $this->mapper->mapToModel($aggregateRoot);

        $restaurantSql = <<<SQL
            INSERT INTO restaurants (
                id,
                name,
                email,
                phone,
                max_number_of_diners,
                min_number_of_diners,
                number_of_tables,
                has_reminders
            )
            VALUES (
                :id,
                :name,
                :email,
                :phone,
                :max_number_of_diners,
                :min_number_of_diners,
                :number_of_tables,
                :has_reminders
            )
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                email = VALUES(email),
                phone = VALUES(phone),
                max_number_of_diners = VALUES(max_number_of_diners),
                min_number_of_diners = VALUES(min_number_of_diners),
                number_of_tables = VALUES(number_of_tables),
                has_reminders = VALUES(has_reminders)
        SQL;

        $restaurantStmt = $this->db->prepare($restaurantSql);
        $restaurantStmt->execute([
            'id' => $restaurantModel->id,
            'name' => $restaurantModel->settings->name,
            'email' => $restaurantModel->settings->email,
            'phone' => $restaurantModel->settings->phone,
            'max_number_of_diners' => $restaurantModel->settings->maxNumberOfDiners,
            'min_number_of_diners' => $restaurantModel->settings->minNumberOfDiners,
            'number_of_tables' => $restaurantModel->settings->numberOfTables,
            'has_reminders' => $restaurantModel->settings->hasReminders ? 1 : 0,
        ]);

        $deleteDiningAreasSql = 'DELETE FROM dining_areas WHERE restaurant_id = :restaurant_id';
        $deleteDiningAreasStmt = $this->db->prepare($deleteDiningAreasSql);
        $deleteDiningAreasStmt->execute(['restaurant_id' => $restaurantModel->id]);

        $deleteAvailabilitiesSql = 'DELETE FROM availabilities WHERE restaurant_id = :restaurant_id';
        $deleteAvailabilitiesStmt = $this->db->prepare($deleteAvailabilitiesSql);
        $deleteAvailabilitiesStmt->execute(['restaurant_id' => $restaurantModel->id]);

        $deleteUsersSql = 'DELETE FROM restaurants_users WHERE restaurant_id = :restaurant_id';
        $deleteUsersStmt = $this->db->prepare($deleteUsersSql);
        $deleteUsersStmt->execute(['restaurant_id' => $restaurantModel->id]);

        if (!empty($restaurantModel->diningAreas)) {
            $insertDiningAreaSql = <<<SQL
                INSERT INTO dining_areas (id, name, capacity, restaurant_id)
                VALUES (:id, :name, :capacity, :restaurant_id)
            SQL;
            $insertDiningAreaStmt = $this->db->prepare($insertDiningAreaSql);
            foreach ($restaurantModel->diningAreas as $diningArea) {
                $insertDiningAreaStmt->execute([
                    'id' => $diningArea->id,
                    'name' => $diningArea->name,
                    'capacity' => $diningArea->capacity,
                    'restaurant_id' => $restaurantModel->id,
                ]);
            }
        }

        if (!empty($restaurantModel->availabilities)) {
            $insertAvailabilitySql = <<<SQL
                INSERT INTO availabilities (id, day_of_week_id, time_slot_id, capacity, restaurant_id)
                VALUES (:id, :day_of_week_id, :time_slot_id, :capacity, :restaurant_id)
            SQL;
            $insertAvailabilityStmt = $this->db->prepare($insertAvailabilitySql);
            foreach ($restaurantModel->availabilities as $availability) {
                $insertAvailabilityStmt->execute([
                    'id' => uniqid(),
                    'day_of_week_id' => $availability->dayOfWeekId,
                    'time_slot_id' => $availability->timeSlotId,
                    'capacity' => $availability->capacity,
                    'restaurant_id' => $restaurantModel->id,
                ]);
            }
        }

        if (!empty($restaurantModel->users)) {
            $insertUserSql = <<<SQL
                INSERT INTO restaurants_users (restaurant_id, user_id)
                VALUES (:restaurant_id, :user_id)
            SQL;
            $insertUserStmt = $this->db->prepare($insertUserSql);
            foreach ($restaurantModel->users as $userEmail) {
                $insertUserStmt->execute([
                    'restaurant_id' => $restaurantModel->id,
                    'user_id' => $userEmail,
                ]);
            }
        }

        foreach ($aggregateRoot->getEvents() as $event) {
            $this->domainEventsBus->publish($event);
        }
    }

    public function getById(string $id): ?Restaurant
    {
        $restaurantSql = 'SELECT * FROM restaurants WHERE id = :id';
        $restaurantStmt = $this->db->prepare($restaurantSql);
        $restaurantStmt->execute(['id' => $id]);

        /** @var array{id: string, name: string, email: string, phone: string, max_number_of_diners: int, min_number_of_diners: int, number_of_tables: int, has_reminders: int|bool}|false $restaurantData */
        $restaurantData = $restaurantStmt->fetch(PDO::FETCH_ASSOC);
        if ($restaurantData === false) {
            return null;
        }

        $diningAreasSql = 'SELECT * FROM dining_areas WHERE restaurant_id = :restaurant_id';
        $diningAreasStmt = $this->db->prepare($diningAreasSql);
        $diningAreasStmt->execute(['restaurant_id' => $id]);

        /** @var array<int, array{id: string, name: string, capacity: int}> $diningAreasData */
        $diningAreasData = $diningAreasStmt->fetchAll(PDO::FETCH_ASSOC);
        $diningAreas = array_map(
            fn (array $data) => new DiningAreaModel(
                id: $data['id'],
                capacity: $data['capacity'],
                name: $data['name'],
            ),
            $diningAreasData
        );

        $availabilitiesSql = 'SELECT * FROM availabilities WHERE restaurant_id = :restaurant_id';
        $availabilitiesStmt = $this->db->prepare($availabilitiesSql);
        $availabilitiesStmt->execute(['restaurant_id' => $id]);

        /** @var array<int, array{capacity: int, day_of_week_id: int, time_slot_id: int}> $availabilitiesData */
        $availabilitiesData = $availabilitiesStmt->fetchAll(PDO::FETCH_ASSOC);
        $availabilities = array_map(
            fn (array $data) => new AvailabilityModel(
                capacity: $data['capacity'],
                dayOfWeekId: $data['day_of_week_id'],
                timeSlotId: $data['time_slot_id'],
            ),
            $availabilitiesData
        );

        $usersSql = 'SELECT user_id FROM restaurants_users WHERE restaurant_id = :restaurant_id';
        $usersStmt = $this->db->prepare($usersSql);
        $usersStmt->execute(['restaurant_id' => $id]);

        /** @var array<int, array{user_id: string}> $usersData */
        $usersData = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
        $users = array_map(fn (array $data) => $data['user_id'], $usersData);

        $restaurantModel = new RestaurantModel(
            id: $restaurantData['id'],
            settings: new SettingsModel(
                email: $restaurantData['email'],
                hasReminders: (bool) $restaurantData['has_reminders'],
                name: $restaurantData['name'],
                maxNumberOfDiners: $restaurantData['max_number_of_diners'],
                minNumberOfDiners: $restaurantData['min_number_of_diners'],
                numberOfTables: $restaurantData['number_of_tables'],
                phone: $restaurantData['phone'],
            ),
            diningAreas: $diningAreas,
            availabilities: $availabilities,
            users: $users,
        );

        return $this->mapper->mapToDomain($restaurantModel);
    }

    /**
     * @return array<Restaurant>
     */
    public function findByUserEmail(string $email): array
    {
        $restaurantIdsSql = <<<SQL
            SELECT DISTINCT restaurant_id
            FROM restaurants_users
            WHERE user_id = :email
        SQL;
        $restaurantIdsStmt = $this->db->prepare($restaurantIdsSql);
        $restaurantIdsStmt->execute(['email' => $email]);

        /** @var array<int, array{restaurant_id: string}> $restaurantIdsData */
        $restaurantIdsData = $restaurantIdsStmt->fetchAll(PDO::FETCH_ASSOC);
        $restaurantIds = array_map(fn (array $data) => $data['restaurant_id'], $restaurantIdsData);

        if (empty($restaurantIds)) {
            return [];
        }

        $restaurants = [];
        foreach ($restaurantIds as $restaurantId) {
            $restaurant = $this->getById($restaurantId);
            if ($restaurant !== null) {
                $restaurants[] = $restaurant;
            }
        }

        return $restaurants;
    }
}
