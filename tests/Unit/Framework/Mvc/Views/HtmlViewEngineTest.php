<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Views;

use Framework\Files\FileManager;
use Framework\Mvc\Actions\Responses\View;
use Framework\Mvc\HtmlViewEngineSettings;
use Framework\Mvc\LanguageSettings;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Requests\RequestContextKeys;
use Framework\Mvc\Responses\StatusCode;
use Framework\Mvc\Security\Identity;
use Framework\Mvc\Views\BranchesReplacer;
use Framework\Mvc\Views\HtmlViewEngine;
use Framework\Mvc\Views\I18nReplacer;
use Framework\Mvc\Views\ModelReplacer;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Framework\Mvc\Fixtures\Views\BranchModel;

#[AllowMockObjectsWithoutExpectations]
final class HtmlViewEngineTest extends TestCase
{
    private string $basePath = __DIR__ . "/Files/";
    private FileManager&MockObject $fileManager;
    private HtmlViewEngine $viewEngine;

    protected function setUp(): void
    {
        $this->fileManager = $this->createMock(FileManager::class);
        $i18nReplacer = new I18nReplacer(
            new LanguageSettings(basePath: __DIR__),
            $this->fileManager,
            new BranchesReplacer(new ModelReplacer())
        );
        $settings = new HtmlViewEngineSettings(basePath: __DIR__, viewPath: "/Files");
        $this->viewEngine = new HtmlViewEngine(settings: $settings, contentReplacer: $i18nReplacer);
    }

    protected function tearDown(): void
    {
    }

    public function testRenderFailWhenViewDoesNotExist(): void
    {
        $view = new View(
            viewPath: "fake_view",
            data: null,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Template\s+not\s+found:\s+.*\/fake_view\.html$/');

        $this->viewEngine->render($view, new RequestContext());
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

        $body = $this->viewEngine->render($view, $this->getRequestContext());

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

        $body = $this->viewEngine->render($view, $this->getRequestContext());

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

        $body = $this->viewEngine->render($view, $this->getRequestContext());

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

        $body = $this->viewEngine->render($view, $this->getRequestContext());

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
        $data->isProperty = true;
        $data->hasItems = true;
        $data->items = ["option 1", "option 2", "option 3"];
        $data->arrayProperty = [
            'my-key' => (object)[
                'name' => 'My Value'
            ]
        ];

        $view = new View(
            viewPath: "branch_view",
            data: $data,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $expected = file_get_contents("{$this->basePath}/branch_view_expected.html");

        $body = $this->viewEngine->render($view, $this->getRequestContext());

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

        $body = $this->viewEngine->render($view, $this->getRequestContext());

        $this->assertSame($expected, $body);
    }

    public function testRenderFailWhenLayoutDoesNotExist(): void
    {
        $model = new \stdClass();
        $view = new View(
            viewPath: "view_without_layout",
            data: $model,
            headers: [],
            statusCode: StatusCode::Ok
        );
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Layout not found: fakelayout");

        $this->viewEngine->render($view, $this->getRequestContext());
    }

    private function getRequestContext(): RequestContext
    {
        $requestContext = new RequestContext();
        $requestContext->set(RequestContextKeys::Language->value, 'en');
        $identity = $this->createMock(Identity::class);
        $identity->method('isAuthenticated')->willReturn(false);
        $identity->method('hasRole')->willReturn(false);
        $identity->method('username')->willReturn('anonymous');
        $requestContext->setIdentity($identity);
        return $requestContext;
    }
}
