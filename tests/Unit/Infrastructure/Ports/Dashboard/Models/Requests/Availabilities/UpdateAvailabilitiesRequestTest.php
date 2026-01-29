<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Availabilities\Requests;

use Application\Restaurants\UpdateAvailabilities\Availability;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Requests\UpdateAvailabilitiesRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class UpdateAvailabilitiesRequestTest extends TestCase
{
    public function testConstructorParsesValidRequestBody(): void
    {
        $parsedBody = [
            '1_2' => '10',
            '3_4' => '20',
            '5_1' => '15',
        ];

        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getParsedBody')->willReturn($parsedBody);

        $updateRequest = new UpdateAvailabilitiesRequest($requestStub);

        $this->assertCount(3, $updateRequest->availabilities);
        $this->assertInstanceOf(Availability::class, $updateRequest->availabilities[0]);
        $this->assertSame(1, $updateRequest->availabilities[0]->timeSlotId);
        $this->assertSame(2, $updateRequest->availabilities[0]->dayOfWeekId);
        $this->assertSame(10, $updateRequest->availabilities[0]->capacity);
    }

    public function testConstructorParsesKeyFormatTimeSlotIdDayOfWeekId(): void
    {
        $parsedBody = [
            '13_5' => '25',
        ];

        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getParsedBody')->willReturn($parsedBody);

        $updateRequest = new UpdateAvailabilitiesRequest($requestStub);

        $this->assertCount(1, $updateRequest->availabilities);
        $this->assertSame(13, $updateRequest->availabilities[0]->timeSlotId);
        $this->assertSame(5, $updateRequest->availabilities[0]->dayOfWeekId);
        $this->assertSame(25, $updateRequest->availabilities[0]->capacity);
    }

    public function testConstructorConvertsNonNumericValueToZeroCapacity(): void
    {
        $parsedBody = [
            '1_2' => 'invalid',
            '3_4' => '',
        ];

        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getParsedBody')->willReturn($parsedBody);

        $updateRequest = new UpdateAvailabilitiesRequest($requestStub);

        $this->assertCount(2, $updateRequest->availabilities);
        $this->assertSame(0, $updateRequest->availabilities[0]->capacity);
        $this->assertSame(0, $updateRequest->availabilities[1]->capacity);
    }

    public function testConstructorHandlesEmptyParsedBody(): void
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getParsedBody')->willReturn([]);

        $updateRequest = new UpdateAvailabilitiesRequest($requestStub);

        $this->assertCount(0, $updateRequest->availabilities);
    }

    public function testConstructorThrowsExceptionForNonArrayParsedBody(): void
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getParsedBody')->willReturn('not an array');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid request body');

        new UpdateAvailabilitiesRequest($requestStub);
    }

    public function testConstructorThrowsExceptionForNullParsedBody(): void
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $requestStub->method('getParsedBody')->willReturn(null);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid request body');

        new UpdateAvailabilitiesRequest($requestStub);
    }
}
