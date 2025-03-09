<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\ValueObjects;

use App\Domain\Shared\Password;
use App\Domain\Users\ValueObjects\Credential;
use Seedwork\Domain\Exceptions\ValueException;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class CredentialTest extends TestCase
{
    private Faker $faker;
    private Password $password;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->password = new Password(value: $this->faker->password(Password::MIN_LENGTH));
    }

    protected function tearDown(): void
    {
    }

    public function testBuildCredential(): void
    {
        $secret = $this->faker->uuid();
        $seed = $this->faker->uuid();

        $credential = Credential::build($secret, $seed);

        $this->assertSame($secret, $credential->secret);
        $this->assertSame($seed, $credential->seed);
    }

    public function testBuildFailWhenSecretIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::build('', $this->faker->uuid());
    }

    public function testBuildFailWhenSeedIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::build($this->faker->uuid(), '');
    }

    public function testCreateInstance(): void
    {
        $seed = $this->faker->uuid();

        $credential = Credential::new($this->password, $seed);

        $this->assertNotEmpty($credential->secret);
        $this->assertSame($seed, $credential->seed);
    }

    public function testCheckWhenPaswordMatches(): void
    {
        $credential = Credential::new($this->password, $this->faker->uuid());

        $this->assertTrue($credential->check($this->password));
    }

    public function testCheckWhenPasswordDoesNotMatch(): void
    {
        $credential = Credential::new($this->password, $this->faker->uuid());

        $this->assertFalse($credential->check(new Password($this->faker->uuid())));
    }
}
