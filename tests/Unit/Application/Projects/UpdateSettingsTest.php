<?php

declare(strict_types=1);

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ProjectRepository;

final class UpdateSettingsTest extends TestCase
{
    private $faker = null;
    private ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->projectRepository = $this->createMock(ProjectRepository::class);
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->projectRepository = null;
    }

    public function testUpdateSettingsShouldUpdateProjectSettings(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenProjectDoesNotExist(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenUserIsNotAuthorized(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenDinersLimitAreInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenNameIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenClaimIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenEmailIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenPhoneIsInvalid(): void
    {
        $this->assertTrue(false);
    }

    public function testUpdateSettingsShouldFailWhenAddressIsInvalid(): void
    {
        $this->assertTrue(false);
    }
}
