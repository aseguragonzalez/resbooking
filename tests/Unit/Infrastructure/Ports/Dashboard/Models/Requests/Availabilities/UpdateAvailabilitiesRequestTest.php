<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Availabilities\Requests;

use Application\Restaurants\UpdateAvailabilities\Availability;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Requests\UpdateAvailabilitiesRequest;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[AllowMockObjectsWithoutExpectations]
final class UpdateAvailabilitiesRequestTest extends TestCase
{
    public function testConstructorParsesValidRequestBody(): void
    {
        $parsedBody = [
            '1_2' => '10',
            '3_4' => '20',
            '5_1' => '15',
        ];

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getParsedBody')->willReturn($parsedBody);

        $updateRequest = new UpdateAvailabilitiesRequest($requestMock);

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

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getParsedBody')->willReturn($parsedBody);

        $updateRequest = new UpdateAvailabilitiesRequest($requestMock);

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

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getParsedBody')->willReturn($parsedBody);

        $updateRequest = new UpdateAvailabilitiesRequest($requestMock);

        $this->assertCount(2, $updateRequest->availabilities);
        $this->assertSame(0, $updateRequest->availabilities[0]->capacity);
        $this->assertSame(0, $updateRequest->availabilities[1]->capacity);
    }

    public function testConstructorHandlesEmptyParsedBody(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getParsedBody')->willReturn([]);

        $updateRequest = new UpdateAvailabilitiesRequest($requestMock);

        $this->assertCount(0, $updateRequest->availabilities);
    }

    public function testConstructorThrowsExceptionForNonArrayParsedBody(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getParsedBody')->willReturn('not an array');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid request body');

        new UpdateAvailabilitiesRequest($requestMock);
    }

    public function testConstructorThrowsExceptionForNullParsedBody(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getParsedBody')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid request body');

        new UpdateAvailabilitiesRequest($requestMock);
    }
}
