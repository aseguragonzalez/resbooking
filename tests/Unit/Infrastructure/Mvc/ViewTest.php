<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Mvc;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\{StatusCode, View};

final class ViewTest extends TestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function testViewShouldReplacePrimitiveProperties(): void
    {
        $data = new \stdClass();
        $data->name = "Peter Parker";
        $data->age = 25;
        $data->height = 1.75;
        $data->isStudent = true;
        $data->isEmployed = false;
        $data->createdAt = new \DateTimeImmutable('2025-01-02T12:01:02.000Z');
        $view = new View(
            path: __DIR__ . '/Files/primitive_properties.html',
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents(__DIR__ . '/Files/primitive_properties_expected.html');

        $body = $view->getBody();

        $this->assertEquals($expected, $body);
    }

    public function testViewShouldReplaceObjectProperty(): void
    {
        $this->markTestSkipped("Not implemented yet.");
    }

    public function testViewShouldReplaceArrayObjectProperty(): void
    {
        $this->markTestSkipped("Not implemented yet.");
    }

    public function testViewShouldAppliesLayout(): void
    {
        $this->markTestSkipped("Not implemented yet.");
    }

    public function testViewShouldAppliesBranch(): void
    {
        $this->markTestSkipped("Not implemented yet.");
    }
}
