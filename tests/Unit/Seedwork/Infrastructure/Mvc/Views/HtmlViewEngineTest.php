<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Views;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;
use Seedwork\Infrastructure\Mvc\Views\{HtmlViewEngine, ViewEngine};
use Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Views\BranchModel;

final class HtmlViewEngineTest extends TestCase
{
    private string $basePath = __DIR__ . "/Files/";

    private ViewEngine $viewEngine;

    protected function setUp(): void
    {
        $this->viewEngine = new HtmlViewEngine(basePath: __DIR__ . "/Files");
    }

    protected function tearDown(): void
    {
    }

    public function testRenderWithPrimitiveProperties(): void
    {
        $model = new \stdClass();
        $model->name = "Peter Parker";
        $model->age = 25;
        $model->height = 1.75;
        $model->isStudent = true;
        $model->isEmployed = false;
        $model->createdAt = new \DateTimeImmutable('2025-01-02T12:01:02.000Z');
        $data = new \stdClass();
        $data->model = $model;
        $view = new View(
            viewPath: "primitive_properties",
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents("{$this->basePath}/primitive_properties_expected.html");

        $body = $this->viewEngine->render($view);

        $this->assertSame($expected, $body);
    }

    public function testRenderObjectProperties(): void
    {
        $address = new \stdClass();
        $address->street = "Elm Street";
        $address->number = 123;
        $address->city = "Springwood";
        $address->state = "Ohio";
        $address->postalCode = "12345-678";
        $address->updatedAt = new \DateTimeImmutable('2025-01-03T12:01:02.000Z');
        $model = new \stdClass();
        $model->name = "Freddy Krueger";
        $model->age = 45;
        $model->height = 1.75;
        $model->address = $address;
        $data = new \stdClass();
        $data->model = $model;
        $view = new View(
            viewPath: "object_properties",
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents("{$this->basePath}/object_properties_expected.html");

        $body = $this->viewEngine->render($view);

        $this->assertSame($expected, $body);
    }

    public function testRenderArrayOfObjects(): void
    {
        $user1 = new \stdClass();
        $user1->id = "1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p";
        $user1->name = "Peter Parker";
        $user1->age = 25;
        $user2 = new \stdClass();
        $user2->id = "2b3c4d5e-6f7g-8h9i-0j1k-2l3m4n5o6p7q";
        $user2->name = "Freddy Mercury";
        $user2->age = 45;
        $model = new \stdClass();
        $model->users = [$user1, $user2];
        $data = new \stdClass();
        $data->model = $model;
        $view = new View(
            viewPath: "array_of_objects",
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents("{$this->basePath}/array_of_objects_expected.html");

        $body = $this->viewEngine->render($view);

        $this->assertSame($expected, $body);
    }

    public function testRenderComplexModel(): void
    {
        $address = new \stdClass();
        $address->street = "Elm Street";
        $address->number = 123;
        $address->city = "Springwood";
        $address->state = "Ohio";
        $customer = new \stdClass();
        $customer->name = "Peter Parker";
        $customer->taxId = "123.456.789-00";
        $customer->phone = "(11) 98765-4321";
        $customer->address = $address;
        $customer->transactions = [
            (object) [
                "id" => "1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p",
                "status" => "approved",
                "amount" => 123.45,
                "createdAt" => new \DateTimeImmutable('2025-01-02T12:01:02.000Z')
            ],
            (object) [
                "id" => "2b3c4d5e-6f7g-8h9i-0j1k-2l3m4n5o6p7q",
                "status" => "declined",
                "amount" => 67.89,
                "createdAt" => new \DateTimeImmutable('2025-01-03T12:01:02.000Z')
            ],
            (object) [
                "id" => "3c4d5e6f-7g8h-9i0j-1k2l-3m4n5o6p7q8r",
                "status" => "declined",
                "amount" => 67.89,
                "createdAt" => new \DateTimeImmutable('2025-01-04T12:01:02.000Z')
            ],
            (object) [
                "id" => "4d5e6f7g-8h9i-0j1k-2l3m-4n5o6p7q8r9s",
                "status" => "approved",
                "amount" => 167.89,
                "createdAt" => new \DateTimeImmutable('2025-01-05T12:01:02.000Z')
            ]
        ];
        $model = new \stdClass();
        $model->title = "Transactions";
        $model->totalAmount = 1234.56;
        $model->updatedAt = new \DateTimeImmutable('2025-01-01T12:01:02.000Z');
        $model->customer = $customer;
        $data = new \stdClass();
        $data->model = $model;
        $view = new View(
            viewPath: "complex_view",
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents("{$this->basePath}/complex_view_expected.html");

        $body = $this->viewEngine->render($view);

        $this->assertSame($expected, $body);
    }

    public function testRenderWithBranchOptions(): void
    {
        $model = new BranchModel(
            name: "Peter Parker",
            description: "Friendly neighborhood Spider",
            isBooleanProperty: true
        );
        $data = new \stdClass();
        $data->model = $model;
        $view = new View(
            viewPath: "branch_view",
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents("{$this->basePath}/branch_view_expected.html");

        $body = $this->viewEngine->render($view);

        $this->assertSame($expected, $body);
    }

    public function testRenderWithLayout(): void
    {
        $model = new \stdClass();
        $model->name = "Peter Parker";
        $model->age = 25;
        $model->height = 1.75;
        $model->title = "Layout Test";
        $data = new \stdClass();
        $data->model = $model;
        $data->pageTitle = "Layout page title";
        $view = new View(
            viewPath: "view_with_layout",
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents("{$this->basePath}/view_with_layout_expected.html");

        $body = $this->viewEngine->render($view);

        $this->assertSame($expected, $body);
    }
}
