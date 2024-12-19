<?php

declare(strict_types=1);

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

use App\Domain\Projects\ValueObjects\Credential;
use App\Seedwork\Domain\Exceptions\ValueException;

final class CredentialTest extends TestCase
{
    private string $message = "This feature is not implemented yet.";

    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testBuildShouldInstantiateCredential(): void
    {
        $secret = $this->faker->uuid();
        $seed = $this->faker->uuid();

        $credential = Credential::build($secret, $seed);

        $this->assertEquals($secret, $credential->secret);
        $this->assertEquals($seed, $credential->seed);
    }

    public function testBuildShouldFailWhenSecretIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::build('', $this->faker->uuid());
    }

    public function testBuildShouldFailWhenSeedIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::build($this->faker->uuid(), '');
    }

    public function testNewShouldCreateCredential(): void
    {
        $phrase = $this->faker->word();
        $seed = $this->faker->uuid();

        $credential = Credential::new($phrase, $seed);

        $this->assertNotEmpty($credential->secret);
        $this->assertEquals($seed, $credential->seed);
    }

    public function testNewShouldFailWhenPhraseIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::new('', $this->faker->uuid());
    }

    public function testNewShouldFailWhenSeedIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        Credential::new($this->faker->word(), '');
    }

    public function testCheckShouldBeTrueWhenPhraseMatches(): void
    {
        $phrase = $this->faker->word();
        $credential = Credential::new($phrase, $this->faker->uuid());

        $this->assertTrue($credential->check($phrase));
    }

    public function testCheckShouldBeFalseWhenPhraseDoesNotMatch(): void
    {
        $credential = Credential::new($this->faker->word(), $this->faker->uuid());

        $this->assertFalse($credential->check($this->faker->word()));
    }

    public function testCheckShouldFailWhenPhraseIsInvalid(): void
    {
        $credential = Credential::new($this->faker->word(), $this->faker->uuid());
        $this->expectException(ValueException::class);

        $credential->check('');
    }
}
