<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\{Project, Place, User};
use App\Domain\Projects\Exceptions\{
    OpenCloseEventAlreadyExists,
    OpenCloseEventDoesNotExists,
    OpenCloseEventOutOfRange,
    PlaceAlreadyExists,
    PlaceDoesNotExists,
    TurnAlreadyExists,
    TurnDoesNotExists,
    UserAlreadyExists,
    UserDoesNotExists
};
use App\Domain\Projects\ValueObjects\{Credential, Settings};
use App\Domain\Shared\{Capacity, DayOfWeek, Email, Password, Phone, Turn};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};

final class ProjectTest extends TestCase
{
    private $faker = null;
    private ?Password $password = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->password = new Password($this->faker->password(Password::MIN_LENGTH));
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    private function project(
        array $users = [],
        array $places = [],
        array $turns = [],
        array $openCloseEvents = []
    ): Project {
        return new Project(
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
            maxNumberOfDiners: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            minNumberOfDiners: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            phone: new Phone($this->faker->phoneNumber)
        );
    }

    public function testConstructShouldCreateInstance(): void
    {
        $id = $this->faker->uuid;
        $settings = $this->settings();

        $project = new Project(id: $id, settings: $settings);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($id, $project->getId());
        $this->assertEquals($settings, $project->getSettings());
        $this->assertEmpty($project->getUsers());
        $this->assertEmpty($project->getPlaces());
        $this->assertEmpty($project->getTurns());
        $this->assertEmpty($project->getOpenCloseEvents());
    }

    public function testAddUserShouldAddUserToProject(): void
    {
        $project = $this->project();
        $credential = Credential::new(password: $this->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);

        $project->addUser($user);

        $this->assertContains($user, $project->getUsers());
    }

    public function testAddUserShouldFailWhenUserAlreadyExists(): void
    {
        $project = $this->project();
        $credential = Credential::new(password: $this->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);
        $project->addUser($user);
        $this->expectException(UserAlreadyExists::class);

        $project->addUser($user);
    }

    public function testRemoveUserShouldRemoveUserFromProject(): void
    {
        $credential = Credential::new(password: $this->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);
        $project = $this->project(users: [$user]);

        $project->removeUser($user);

        $this->assertEmpty($project->getUsers());
    }

    public function testRemoveUserShouldFailWhenUserDoesNotExists(): void
    {
        $project = $this->project();
        $credential = Credential::new(password: $this->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);
        $this->expectException(UserDoesNotExists::class);

        $project->removeUser($user);
    }

    public function testAddPlaceShouldAddPlaceToProject(): void
    {
        $project = $this->project();
        $place = new Place(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber())
        );

        $project->addPlace($place);

        $this->assertContains($place, $project->getPlaces());
    }

    public function testAddPlaceShouldFailWhenPlaceAlreadyExists(): void
    {
        $place = new Place(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber())
        );
        $project = $this->project(places: [$place]);
        $this->expectException(PlaceAlreadyExists::class);

        $project->addPlace($place);
    }

    public function testRemovePlaceShouldRemovePlaceFromProject(): void
    {
        $place = new Place(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber())
        );
        $project = $this->project(places: [$place]);

        $project->removePlace($place);

        $this->assertEmpty($project->getPlaces());
    }

    public function testRemovePlaceShouldFailWhenPlaceDoesNotExists(): void
    {
        $project = $this->project();
        $place = new Place(
            id: $this->faker->uuid,
            name: $this->faker->name,
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber())
        );
        $this->expectException(PlaceDoesNotExists::class);

        $project->removePlace($place);
    }

    public function testAddTurnShouldAddTurnToProject(): void
    {
        $project = $this->project();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );

        $project->addTurn($turn);

        $this->assertContains($turn, $project->getTurns());
    }

    public function testAddTurnShouldFailWhenTurnAlreadyExists(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );
        $project = $this->project(turns: [$turn]);
        $this->expectException(TurnAlreadyExists::class);

        $project->addTurn($turn);
    }

    public function testRemoveTurnShouldRemoveTurnFromProject(): void
    {
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );
        $project = $this->project(turns: [$turn]);

        $project->removeTurn($turn);

        $this->assertEmpty($project->getTurns());
    }

    public function testRemoveTurnShouldFailWhenTurnDoesNotExists(): void
    {
        $project = $this->project();
        $turn = new TurnAvailability(
            capacity: new Capacity($this->faker->randomNumber(), $this->faker->randomNumber()),
            dayOfWeek: DayOfWeek::MONDAY,
            turn: Turn::H1200,
        );
        $this->expectException(TurnDoesNotExists::class);

        $project->removeTurn($turn);
    }

    public function testAddOpenCloseEventShouldAddOpenCloseEventToProject(): void
    {
        $project = $this->project();
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );

        $project->addOpenCloseEvent($openCloseEvent);

        $this->assertContains($openCloseEvent, $project->getOpenCloseEvents());
    }

    public function testAddOpenCloseEventShouldFailWhenOpenCloseEventAlreadyExists(): void
    {
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );
        $project = $this->project(openCloseEvents: [$openCloseEvent]);
        $this->expectException(OpenCloseEventAlreadyExists::class);

        $project->addOpenCloseEvent($openCloseEvent);
    }

    public function testAddOpenCloseEventShouldFailWhenDateIsOutOfRange(): void
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

    public function testRemoveOpenCloseEventShouldRemoveOpenCloseEventFromProject(): void
    {
        $openCloseEvent = new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        );
        $project = $this->project(openCloseEvents: [$openCloseEvent]);

        $project->removeOpenCloseEvent($openCloseEvent);

        $this->assertEmpty($project->getOpenCloseEvents());
    }

    public function testRemoveOpenCloseEventShouldFailWhenOpenCloseEventDoesNotExists(): void
    {
        $project = $this->project();
        $this->expectException(OpenCloseEventDoesNotExists::class);

        $project->removeOpenCloseEvent(new OpenCloseEvent(
            date: new \DateTimeImmutable(),
            isAvailable: $this->faker->boolean,
            turn: Turn::H1200,
        ));
    }
}
