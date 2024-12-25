<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\{Project, Place, User};
use App\Domain\Projects\Exceptions\{UserAlreadyExistsException, UserDoesNotExistsException};
use App\Domain\Projects\ValueObjects\{Credential, Settings};
use App\Domain\Shared\{Capacity, Email, Phone, Turn, DayOfWeek};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use DateTime;

final class ProjectTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    private function project(
        array $users = [],
        array $places = [],
    ): Project {
        return new Project(
            id: $this->faker->uuid,
            settings: $this->settings(),
            users: $users,
            places: $places,
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
        $credential = Credential::new(phrase: $this->faker->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);

        $project->addUser($user);

        $this->assertContains($user, $project->getUsers());
    }

    public function testAddUserShouldFailWhenUserAlreadyExists(): void
    {
        $project = $this->project();
        $credential = Credential::new(phrase: $this->faker->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);
        $project->addUser($user);
        $this->expectException(UserAlreadyExistsException::class);

        $project->addUser($user);
    }

    public function testRemoveUserShouldRemoveUserFromProject(): void
    {
        $credential = Credential::new(phrase: $this->faker->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);
        $project = $this->project(users: [$user]);

        $project->removeUser($user);

        $this->assertContains($user, $project->getUsers());
    }

    public function testRemoveUserShouldFailWhenUserDoesNotExists(): void
    {
        $project = $this->project();
        $credential = Credential::new(phrase: $this->faker->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);
        $this->expectException(UserDoesNotExistsException::class);

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
        $this->markTestIncomplete('Not implemented yet.');
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
        $this->markTestIncomplete('Not implemented yet.');
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
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveTurnShouldRemoveTurnFromProject(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveTurnShouldFailWhenTurnDoesNotExists(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
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
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveOpenCloseEventShouldRemoveOpenCloseEventFromProject(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveOpenCloseEventShouldFailWhenOpenCloseEventDoesNotExists(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
