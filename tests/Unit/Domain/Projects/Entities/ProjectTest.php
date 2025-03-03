<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use App\Domain\Projects\Entities\{Project, Place};
use App\Domain\Projects\Events\{
    OpenCloseEventCreated,
    OpenCloseEventRemoved,
    PlaceCreated,
    PlaceRemoved,
    ProjectCreated,
    ProjectModified,
    TurnAssigned,
    TurnUnassigned,
    UserCreated,
    UserRemoved
};
use App\Domain\Projects\Exceptions\{
    UserAlreadyExist,
    UserDoesNotExist,
    PlaceAlreadyExist,
    PlaceDoesNotExist
};
use App\Domain\Projects\ValueObjects\{Settings, User};
use App\Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist
};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use App\Domain\Shared\{Capacity, DayOfWeek, Email, Phone, Turn};
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    /**
     * @param array<User> $users
     * @param array<Place> $places
     * @param array<TurnAvailability> $turns
     * @param array<OpenCloseEvent> $openCloseEvents
     */
    private function project(
        array $users = [],
        array $places = [],
        array $turns = [],
        array $openCloseEvents = []
    ): Project {
        return Project::build(
            id: $this->faker->uuid,
            settings: $this->settings(),
            users: $users,
            places: $places,
            turns: $turns,
            openCloseEvents: $openCloseEvents
        );
    }

    private function settings(): Settings
    {
        return new Settings(
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(8),
            minNumberOfDiners: new Capacity(1),
            numberOfTables: new Capacity(25),
            phone: new Phone($this->faker->phoneNumber)
        );
    }

    public function testCreateInstance(): void
    {
        $id = $this->faker->uuid;
        $settings = $this->settings();

        $project = Project::new(id: $id, settings: $settings);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame($id, $project->getId());
        $this->assertSame($settings, $project->getSettings());
        $this->assertEmpty($project->getUsers());
        $this->assertEmpty($project->getPlaces());
        $this->assertEmpty($project->getTurns());
        $this->assertEmpty($project->getOpenCloseEvents());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(ProjectCreated::class, $events[0]);
        $event = $events[0];
        $this->assertSame($project, $event->getPayload()['project']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testAddUserToProject(): void
    {
        $project = $this->project();
        $user = new User(username: new Email($this->faker->email));

        $project->addUser($user);

        $this->assertContains($user, $project->getUsers());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(UserCreated::class, $events[0]);
        $event = $events[0];
        $this->assertSame($user, $event->getPayload()['user']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testAddUserFailWhenUserAlreadyExist(): void
    {
        $project = $this->project();
        $user = new User(username: new Email($this->faker->email));
        $project->addUser($user);
        $this->expectException(UserAlreadyExist::class);

        $project->addUser($user);
    }

    public function testRemoveUserFromProject(): void
    {
        $user = new User(username: new Email($this->faker->email));
        $project = $this->project(users: [$user]);

        $project->removeUser($user);

        $this->assertEmpty($project->getUsers());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(UserRemoved::class, $events[0]);
        $event = $events[0];
        $this->assertSame($user, $event->getPayload()['user']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testRemoveUserFailWhenUserDoesNotExist(): void
    {
        $project = $this->project();
        $user = new User(username: new Email($this->faker->email));
        $this->expectException(UserDoesNotExist::class);

        $project->removeUser($user);
    }

    public function testAddPlaceToProject(): void
    {
        $project = $this->project();
        $place = Place::new(name: $this->faker->name, capacity: new Capacity(value: 100));

        $project->addPlace($place);

        $this->assertContains($place, $project->getPlaces());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(PlaceCreated::class, $events[0]);
        $event = $events[0];
        $this->assertSame($place, $event->getPayload()['place']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testAddPlaceFailWhenPlaceAlreadyExist(): void
    {
        $place = Place::new(name: $this->faker->name, capacity: new Capacity(value: 100));
        $project = $this->project(places: [$place]);
        $this->expectException(PlaceAlreadyExist::class);

        $project->addPlace($place);
    }

    public function testRemovePlaceFromProject(): void
    {
        $place = Place::new(name: $this->faker->name, capacity: new Capacity(value: 100));
        $project = $this->project(places: [$place]);

        $project->removePlace($place);

        $this->assertEmpty($project->getPlaces());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(PlaceRemoved::class, $events[0]);
        $event = $events[0];
        $this->assertSame($place, $event->getPayload()['place']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testRemovePlaceFailWhenPlaceDoesNotExist(): void
    {
        $project = $this->project();
        $place = Place::new(name: $this->faker->name, capacity: new Capacity(value: 100));
        $this->expectException(PlaceDoesNotExist::class);

        $project->removePlace($place);
    }

    public function testAddTurnToProject(): void
    {
        $project = $this->project();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );

        $project->addTurn($turn);

        $this->assertContains($turn, $project->getTurns());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(TurnAssigned::class, $events[0]);
        $event = $events[0];
        $this->assertSame($turn, $event->getPayload()['turn']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testAddTurnFailWhenTurnAlreadyExist(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );
        $project = $this->project(turns: [$turn]);
        $this->expectException(TurnAlreadyExist::class);

        $project->addTurn($turn);
    }

    public function testRemoveTurnFromProject(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );
        $project = $this->project(turns: [$turn]);

        $project->removeTurn($turn);

        $this->assertEmpty($project->getTurns());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(TurnUnassigned::class, $events[0]);
        $event = $events[0];
        $this->assertSame($turn, $event->getPayload()['turn']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testRemoveTurnFailWhenTurnDoesNotExist(): void
    {
        $project = $this->project();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::Monday,
            turn: Turn::H1200,
        );
        $this->expectException(TurnDoesNotExist::class);

        $project->removeTurn($turn);
    }

    public function testAddOpenCloseEventToProject(): void
    {
        $project = $this->project();
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );

        $project->addOpenCloseEvent($openCloseEvent);

        $this->assertContains($openCloseEvent, $project->getOpenCloseEvents());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(OpenCloseEventCreated::class, $events[0]);
        $event = $events[0];
        $this->assertSame($openCloseEvent, $event->getPayload()['openCloseEvent']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testAddOpenCloseEventFailWhenOpenCloseEventAlreadyExist(): void
    {
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );
        $project = $this->project(openCloseEvents: [$openCloseEvent]);
        $this->expectException(OpenCloseEventAlreadyExist::class);

        $project->addOpenCloseEvent($openCloseEvent);
    }

    public function testAddOpenCloseEventFailWhenDateIsOutOfRange(): void
    {
        $project = $this->project();
        $date = new \DateTimeImmutable();
        $this->expectException(OpenCloseEventOutOfRange::class);

        $project->addOpenCloseEvent(new OpenCloseEvent(
            date: $date->sub(new \DateInterval('P1D')),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        ));
    }

    public function testRemoveOpenCloseEventFromProject(): void
    {
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );
        $project = $this->project(openCloseEvents: [$openCloseEvent]);

        $project->removeOpenCloseEvent($openCloseEvent);

        $this->assertEmpty($project->getOpenCloseEvents());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(OpenCloseEventRemoved::class, $events[0]);
        $event = $events[0];
        $this->assertSame($openCloseEvent, $event->getPayload()['openCloseEvent']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testRemoveOpenCloseEventFailWhenOpenCloseEventDoesNotExist(): void
    {
        $project = $this->project();
        $this->expectException(OpenCloseEventDoesNotExist::class);

        $project->removeOpenCloseEvent(new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        ));
    }

    public function testUpdateProjectSettings(): void
    {
        $project = $this->project();
        $settings = $this->settings();

        $project->updateSettings($settings);

        $this->assertSame($settings, $project->getSettings());
        $events = $project->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(ProjectModified::class, $events[0]);
        $event = $events[0];
        $this->assertSame($project, $event->getPayload()['project']);
        $this->assertSame($project->getId(), $event->getPayload()['projectId']);
    }

    public function testRemoveUsersFromProject(): void
    {
        $email = new Email($this->faker->email);
        $users = [
            new User(username: new Email($this->faker->email)),
            new User(username: $email),
            new User(username: new Email($this->faker->email)),
        ];
        $project = $this->project(users: $users);

        $project->removeUsers(fn(User $user) => $user->username === $email);

        $this->assertNotContains($users[1], $project->getUsers());
        $this->assertCount(1, $project->getEvents());
    }

    public function testRemovePlacesFromProject(): void
    {
        $name = $this->faker->name;
        $places = [
            Place::new(name: $this->faker->name, capacity: new Capacity(value: 100)),
            Place::new(name: $name, capacity: new Capacity(value: 100)),
            Place::new(name: $this->faker->name, capacity: new Capacity(value: 100)),
        ];
        $project = $this->project(places: $places);

        $project->removePlaces(fn(Place $place) => $place->name === $name);

        $this->assertNotContains($places[1], $project->getPlaces());
        $this->assertCount(1, $project->getEvents());
    }

    public function testRemoveTurnsFromProject(): void
    {
        $turns = [
            new TurnAvailability(
                capacity: new Capacity($this->faker->randomNumber()),
                dayOfWeek: DayOfWeek::Monday,
                turn: Turn::H1200,
            ),
            new TurnAvailability(
                capacity: new Capacity($this->faker->randomNumber()),
                dayOfWeek: DayOfWeek::Monday,
                turn: Turn::H1230,
            ),
            new TurnAvailability(
                capacity: new Capacity($this->faker->randomNumber()),
                dayOfWeek: DayOfWeek::Monday,
                turn: Turn::H1200,
            ),
        ];
        $project = $this->project(turns: $turns);

        $project->removeTurns(fn(TurnAvailability $turn) => $turn->turn === Turn::H1230);

        $this->assertNotContains($turns[1], $project->getTurns());
        $this->assertCount(1, $project->getEvents());
    }

    public function testRemoveOpenCloseEventsFromProject(): void
    {
        $openCloseEvents = [
            new OpenCloseEvent(
                date: new \DateTimeImmutable(),
                isAvailable: $this->faker->boolean,
                turn: Turn::H1200,
            ),
            new OpenCloseEvent(
                date: new \DateTimeImmutable(),
                isAvailable: $this->faker->boolean,
                turn: Turn::H1230,
            ),
            new OpenCloseEvent(
                date: new \DateTimeImmutable(),
                isAvailable: $this->faker->boolean,
                turn: Turn::H1200,
            ),
        ];
        $project = $this->project(openCloseEvents: $openCloseEvents);

        $project->removeOpenCloseEvents(fn(OpenCloseEvent $openCloseEvent) => $openCloseEvent->turn === Turn::H1230);

        $this->assertNotContains($openCloseEvents[1], $project->getOpenCloseEvents());
        $this->assertCount(1, $project->getEvents());
    }
}
