<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\ValueObjects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Users\ValueObjects\Credential;
use App\Domain\Shared\Password;
use App\Seedwork\Domain\Exceptions\ValueException;

final class CredentialTest extends TestCase
{
    private $faker = null;
    private ?Password $password = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->password = new Password(value: $this->faker->password(Password::MIN_LENGTH));
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->password = null;
    }

    public function testBuildShouldInstantiateCredential(): void
    {
        $secret = $this->faker->uuid();
        $seed = $this->faker->uuid();

        $credential = Credential::stored($secret, $seed);

        $this->assertEquals($secret, $credential->secret);
        $this->assertEquals($seed, $credential->seed);
    }

    public function testBuildShouldFailWhenSecretIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::stored('', $this->faker->uuid());
    }

    public function testBuildShouldFailWhenSeedIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::stored($this->faker->uuid(), '');
    }

    public function testNewShouldCreateCredential(): void
    {
        $seed = $this->faker->uuid();

        $credential = Credential::new($this->password, $seed);

        $this->assertNotEmpty($credential->secret);
        $this->assertEquals($seed, $credential->seed);
    }

    public function testCheckShouldBeTrueWhenPhraseMatches(): void
    {
        $credential = Credential::new($this->password, $this->faker->uuid());

        $this->assertTrue($credential->check($this->password));
    }

    public function testCheckShouldBeFalseWhenPhraseDoesNotMatch(): void
    {
        $credential = Credential::new($this->password, $this->faker->uuid());

        $this->assertFalse($credential->check(new Password($this->faker->uuid())));
    }
}
