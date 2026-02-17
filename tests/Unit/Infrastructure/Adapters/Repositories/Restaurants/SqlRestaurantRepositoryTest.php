<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Adapters\Repositories\Restaurants;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\TimeSlot;
use Seedwork\Domain\EntityId;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Adapters\Repositories\Restaurants\RestaurantsMapper;
use Infrastructure\Adapters\Repositories\Restaurants\SqlRestaurantRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Application\DomainEventsBus;
use Seedwork\Domain\DomainEvent;
use Tests\Unit\RestaurantBuilder;

final class SqlRestaurantRepositoryTest extends TestCase
{
    private PDO&MockObject $pdo;
    private RestaurantsMapper $mapper;
    private SqlRestaurantRepository $repository;
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;
    /** @var array<PDOStatement> */
    private array $prepareStatementQueue = [];

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $domainEventsBus = $this->createStub(DomainEventsBus::class);
        $domainEventsBus->method('publish')->willReturnCallback(function (): void {
        });
        $this->mapper = new RestaurantsMapper();
        $this->repository = new SqlRestaurantRepository($this->pdo, $domainEventsBus, $this->mapper);
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
        $this->prepareStatementQueue = [];
    }

    public function testSaveInsertsNewRestaurant(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $restaurantStmt = $this->createMock(PDOStatement::class);
        $restaurantStmt
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (array $params) use ($restaurant): bool {
                return $params['id'] === $restaurant->getId()->value
                    && $params['name'] === $restaurant->getSettings()->name
                    && $params['email'] === $restaurant->getSettings()->email->value
                    && $params['phone'] === $restaurant->getSettings()->phone->value
                    && $params['max_number_of_diners'] === $restaurant->getSettings()->maxNumberOfDiners->value
                    && $params['min_number_of_diners'] === $restaurant->getSettings()->minNumberOfDiners->value
                    && $params['number_of_tables'] === $restaurant->getSettings()->numberOfTables->value
                    && $params['has_reminders'] === ($restaurant->getSettings()->hasReminders ? 1 : 0);
            }));
        $this->prepareStatementQueue[] = $restaurantStmt;
        $deleteDiningAreasStmt = $this->createMock(PDOStatement::class);
        $deleteDiningAreasStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurant->getId()->value]);
        $this->prepareStatementQueue[] = $deleteDiningAreasStmt;

        $deleteAvailabilitiesStmt = $this->createMock(PDOStatement::class);
        $deleteAvailabilitiesStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurant->getId()->value]);
        $this->prepareStatementQueue[] = $deleteAvailabilitiesStmt;

        $deleteUsersStmt = $this->createMock(PDOStatement::class);
        $deleteUsersStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurant->getId()->value]);
        $this->prepareStatementQueue[] = $deleteUsersStmt;
        $this->setupPrepareCallback();

        $this->repository->save($restaurant);
    }

    public function testSaveInsertsDiningAreas(): void
    {
        $diningArea1 = DiningArea::new(
            capacity: new Capacity(10),
            name: 'Area 1',
            id: 'area1'
        );
        $diningArea2 = DiningArea::new(
            capacity: new Capacity(20),
            name: 'Area 2',
            id: 'area2'
        );
        $restaurant = $this->restaurantBuilder
            ->withDiningAreas([$diningArea1, $diningArea2])
            ->build();
        $this->mockSaveStatements($restaurant);
        $insertDiningAreaStmt = $this->createMock(PDOStatement::class);
        $insertDiningAreaStmt->expects($this->exactly(2))
            ->method('execute')
            ->with($this->callback(function (array $params) use ($restaurant): bool {
                return isset($params['id'])
                    && isset($params['name'])
                    && isset($params['capacity'])
                    && $params['restaurant_id'] === $restaurant->getId()->value;
            }));
        $this->prepareStatementQueue[] = $insertDiningAreaStmt;
        $this->setupPrepareCallback();

        $this->repository->save($restaurant);
    }

    public function testSaveInsertsAvailabilities(): void
    {
        $availability1 = new Availability(
            capacity: new Capacity(5),
            dayOfWeek: DayOfWeek::Monday,
            timeSlot: TimeSlot::H1200
        );
        $availability2 = new Availability(
            capacity: new Capacity(10),
            dayOfWeek: DayOfWeek::Tuesday,
            timeSlot: TimeSlot::H1300
        );
        $restaurant = $this->restaurantBuilder
            ->withAvailabilities([$availability1, $availability2])
            ->withDiningAreas([])
            ->build();
        $this->mockSaveStatements($restaurant);
        $insertAvailabilityStmt = $this->createMock(PDOStatement::class);
        $insertAvailabilityStmt->expects($this->exactly(2))
            ->method('execute')
            ->with($this->callback(function (array $params) use ($restaurant): bool {
                return isset($params['id'])
                    && isset($params['day_of_week_id'])
                    && isset($params['time_slot_id'])
                    && isset($params['capacity'])
                    && $params['restaurant_id'] === $restaurant->getId()->value;
            }));
        $this->prepareStatementQueue[] = $insertAvailabilityStmt;
        $this->setupPrepareCallback();

        $this->repository->save($restaurant);
    }

    public function testSaveInsertsUsers(): void
    {
        $user1 = new User(new Email('user1@example.com'));
        $user2 = new User(new Email('user2@example.com'));
        $restaurant = $this->restaurantBuilder
            ->withUsers([$user1, $user2])
            ->withDiningAreas([])
            ->withAvailabilities([])
            ->build();
        $this->mockSaveStatements($restaurant);
        $insertUserStmt = $this->createMock(PDOStatement::class);
        $insertUserStmt
            ->expects($this->exactly(2))
            ->method('execute')
            ->with($this->callback(function (array $params) use ($restaurant): bool {
                return $params['restaurant_id'] === $restaurant->getId()->value
                    && isset($params['user_id']);
            }));
        $this->prepareStatementQueue[] = $insertUserStmt;
        $this->setupPrepareCallback();

        $this->repository->save($restaurant);
    }

    public function testSavePublishesDomainEventsFromAggregate(): void
    {
        $restaurant = Restaurant::new('test@example.com');
        $this->assertGreaterThan(
            0,
            count($restaurant->getEvents()),
            'Restaurant::new() should have raised at least one event'
        );
        $restaurantWithEvent = Restaurant::new('publish-test@example.com');
        $domainEventsBus = $this->createMock(DomainEventsBus::class);
        $domainEventsBus
            ->expects($this->once())
            ->method('publish')
            ->with($this->isInstanceOf(DomainEvent::class));
        $this->mockSaveStatements($restaurantWithEvent);
        $this->setupPrepareCallback();
        $repository = new SqlRestaurantRepository($this->pdo, $domainEventsBus, $this->mapper);

        $repository->save($restaurantWithEvent);
    }

    public function testGetByIdReturnsRestaurantWhenExists(): void
    {
        $restaurantId = $this->faker->uuid;
        $email = $this->faker->email;
        $restaurantStmt = $this->createMock(PDOStatement::class);
        $restaurantStmt->expects($this->once())
            ->method('execute')
            ->with(['id' => $restaurantId]);
        $restaurantStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => $restaurantId,
                'name' => 'Test Restaurant',
                'email' => $email,
                'phone' => '+34-555-0100',
                'max_number_of_diners' => 8,
                'min_number_of_diners' => 1,
                'number_of_tables' => 20,
                'has_reminders' => 1,
            ]);
        $this->prepareStatementQueue[] = $restaurantStmt;
        $diningAreasStmt = $this->createMock(PDOStatement::class);
        $diningAreasStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurantId]);
        $diningAreasStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->prepareStatementQueue[] = $diningAreasStmt;
        $availabilitiesStmt = $this->createMock(PDOStatement::class);
        $availabilitiesStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurantId]);
        $availabilitiesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->prepareStatementQueue[] = $availabilitiesStmt;
        $usersStmt = $this->createMock(PDOStatement::class);
        $usersStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurantId]);
        $usersStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->prepareStatementQueue[] = $usersStmt;

        $this->setupPrepareCallback();

        /** @var Restaurant $result */
        $result = $this->repository->getById(EntityId::fromString($restaurantId));

        $this->assertSame($restaurantId, $result->getId()->value);
        $this->assertSame('Test Restaurant', $result->getSettings()->name);
        $this->assertSame($email, $result->getSettings()->email->value);
        $this->assertInstanceOf(Restaurant::class, $result);
    }

    public function testGetByIdReturnsNullWhenNotExists(): void
    {
        $restaurantId = $this->faker->uuid;

        $restaurantStmt = $this->createMock(PDOStatement::class);
        $restaurantStmt->expects($this->once())
            ->method('execute')
            ->with(['id' => $restaurantId]);
        $restaurantStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);
        $this->prepareStatementQueue[] = $restaurantStmt;

        $this->setupPrepareCallback();

        $result = $this->repository->getById(EntityId::fromString($restaurantId));

        $this->assertNull($result);
    }

    public function testGetByIdFetchesDiningAreas(): void
    {
        $restaurantId = $this->faker->uuid;
        $email = $this->faker->email;

        $restaurantStmt = $this->createMock(PDOStatement::class);
        $restaurantStmt->expects($this->once())
            ->method('execute')
            ->with(['id' => $restaurantId]);
        $restaurantStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => $restaurantId,
                'name' => 'Test Restaurant',
                'email' => $email,
                'phone' => '+34-555-0100',
                'max_number_of_diners' => 8,
                'min_number_of_diners' => 1,
                'number_of_tables' => 20,
                'has_reminders' => 1,
            ]);
        $this->prepareStatementQueue[] = $restaurantStmt;

        $diningAreasStmt = $this->createMock(PDOStatement::class);
        $diningAreasStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurantId]);
        $diningAreasStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 'area1',
                    'name' => 'Area 1',
                    'capacity' => 10,
                ],
                [
                    'id' => 'area2',
                    'name' => 'Area 2',
                    'capacity' => 20,
                ],
            ]);
        $this->prepareStatementQueue[] = $diningAreasStmt;

        $availabilitiesStmt = $this->createMock(PDOStatement::class);
        $availabilitiesStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurantId]);
        $availabilitiesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->prepareStatementQueue[] = $availabilitiesStmt;

        $usersStmt = $this->createMock(PDOStatement::class);
        $usersStmt->expects($this->once())
            ->method('execute')
            ->with(['restaurant_id' => $restaurantId]);
        $usersStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->prepareStatementQueue[] = $usersStmt;

        $this->setupPrepareCallback();

        $result = $this->repository->getById(EntityId::fromString($restaurantId));

        $this->assertInstanceOf(Restaurant::class, $result);
        $diningAreas = $result->getDiningAreas();
        $this->assertCount(2, $diningAreas);
        $this->assertSame('area1', $diningAreas[0]->id->value);
        $this->assertSame('Area 1', $diningAreas[0]->name);
        $this->assertSame(10, $diningAreas[0]->capacity->value);
    }

    public function testFindByUserEmailReturnsRestaurants(): void
    {
        $email = 'user@example.com';
        $restaurantId1 = $this->faker->uuid;
        $restaurantId2 = $this->faker->uuid;
        $restaurantIdsStmt = $this->createMock(PDOStatement::class);
        $restaurantIdsStmt->expects($this->once())
            ->method('execute')
            ->with(['email' => $email]);
        $restaurantIdsStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                ['restaurant_id' => $restaurantId1],
                ['restaurant_id' => $restaurantId2],
            ]);
        $this->prepareStatementQueue[] = $restaurantIdsStmt;

        for ($i = 0; $i < 2; $i++) {
            $restaurantId = $i === 0 ? $restaurantId1 : $restaurantId2;

            $restaurantStmt = $this->createMock(PDOStatement::class);
            $restaurantStmt->expects($this->once())
                ->method('execute')
                ->with(['id' => $restaurantId]);
            $restaurantStmt->expects($this->once())
                ->method('fetch')
                ->with(PDO::FETCH_ASSOC)
                ->willReturn([
                    'id' => $restaurantId,
                    'name' => "Restaurant $i",
                    'email' => $this->faker->email,
                    'phone' => '+34-555-0100',
                    'max_number_of_diners' => 8,
                    'min_number_of_diners' => 1,
                    'number_of_tables' => 20,
                    'has_reminders' => 1,
                ]);
            $this->prepareStatementQueue[] = $restaurantStmt;

            $diningAreasStmt = $this->createMock(PDOStatement::class);
            $diningAreasStmt->expects($this->once())
                ->method('execute')
                ->with(['restaurant_id' => $restaurantId]);
            $diningAreasStmt->expects($this->once())
                ->method('fetchAll')
                ->with(PDO::FETCH_ASSOC)
                ->willReturn([]);
            $this->prepareStatementQueue[] = $diningAreasStmt;

            $availabilitiesStmt = $this->createMock(PDOStatement::class);
            $availabilitiesStmt->expects($this->once())
                ->method('execute')
                ->with(['restaurant_id' => $restaurantId]);
            $availabilitiesStmt->expects($this->once())
                ->method('fetchAll')
                ->with(PDO::FETCH_ASSOC)
                ->willReturn([]);
            $this->prepareStatementQueue[] = $availabilitiesStmt;

            $usersStmt = $this->createMock(PDOStatement::class);
            $usersStmt->expects($this->once())
                ->method('execute')
                ->with(['restaurant_id' => $restaurantId]);
            $usersStmt->expects($this->once())
                ->method('fetchAll')
                ->with(PDO::FETCH_ASSOC)
                ->willReturn([]);
            $this->prepareStatementQueue[] = $usersStmt;
        }

        $this->setupPrepareCallback();

        $result = $this->repository->findByUserEmail($email);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Restaurant::class, $result[0]);
        $this->assertInstanceOf(Restaurant::class, $result[1]);
    }

    public function testFindByUserEmailReturnsEmptyArrayWhenNoRestaurants(): void
    {
        $email = 'user@example.com';

        $restaurantIdsStmt = $this->createMock(PDOStatement::class);
        $restaurantIdsStmt->expects($this->once())
            ->method('execute')
            ->with(['email' => $email]);
        $restaurantIdsStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->prepareStatementQueue[] = $restaurantIdsStmt;

        $this->setupPrepareCallback();

        $result = $this->repository->findByUserEmail($email);

        $this->assertEmpty($result);
    }

    private function mockSaveStatements(Restaurant $restaurant): void
    {
        $restaurantStmt = $this->createMock(PDOStatement::class);
        $restaurantStmt->expects($this->once())->method('execute');
        $this->prepareStatementQueue[] = $restaurantStmt;

        $deleteDiningAreasStmt = $this->createMock(PDOStatement::class);
        $deleteDiningAreasStmt->expects($this->once())->method('execute');
        $this->prepareStatementQueue[] = $deleteDiningAreasStmt;

        $deleteAvailabilitiesStmt = $this->createMock(PDOStatement::class);
        $deleteAvailabilitiesStmt->expects($this->once())->method('execute');
        $this->prepareStatementQueue[] = $deleteAvailabilitiesStmt;

        $deleteUsersStmt = $this->createMock(PDOStatement::class);
        $deleteUsersStmt->expects($this->once())->method('execute');
        $this->prepareStatementQueue[] = $deleteUsersStmt;
    }

    private function setupPrepareCallback(): void
    {
        $this->pdo->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnCallback(function (string $sql): PDOStatement {
                if (empty($this->prepareStatementQueue)) {
                    $fallbackStmt = $this->createStub(PDOStatement::class);
                    $fallbackStmt->method('execute')->willReturn(true);
                    $fallbackStmt->method('fetch')->willReturn(false);
                    $fallbackStmt->method('fetchAll')->willReturn([]);
                    return $fallbackStmt;
                }

                return array_shift($this->prepareStatementQueue);
            });
    }
}
