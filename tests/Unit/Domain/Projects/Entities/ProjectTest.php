<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\{Project, Place, User};
use App\Domain\Projects\Exceptions\UserAlreadyExistsException;
use App\Domain\Projects\ValueObjects\{Credential, Settings};
use App\Domain\Shared\{Capacity, Email, Phone};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};


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

    private function project(): Project
    {
        return new Project(id: $this->faker->uuid, settings: $this->settings());
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
        $this->expectException(UserAlreadyExistsException::class);
        $project = $this->project();
        $credential = Credential::new(phrase: $this->faker->password, seed: $this->faker->uuid);
        $user = User::createNewAdmin(username: new Email($this->faker->email), credential: $credential);
        $project->addUser($user);

        $project->addUser($user);
    }

    public function testRemoveUserShouldRemoveUserFromProject(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemoveUserShouldFailWhenUserDoesNotExists(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldAddPlaceToProject(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddPlaceShouldFailWhenPlaceAlreadyExists(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemovePlaceShouldRemovePlaceFromProject(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRemovePlaceShouldFailWhenPlaceDoesNotExists(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testAddTurnShouldAddTurnToProject(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
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
        $this->markTestIncomplete('Not implemented yet.');
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
