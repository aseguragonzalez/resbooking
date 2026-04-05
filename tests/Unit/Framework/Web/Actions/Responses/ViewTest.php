<?php

namespace Tests\Unit\Framework\Actions\Responses\Views;

use PHPUnit\Framework\TestCase;
use Framework\Actions\Responses\View;
use Framework\Responses\Headers\ContentType;
use Framework\Responses\StatusCode;

final class ViewTest extends TestCase
{
    public function testCreateView(): void
    {
        $view = new View(
            viewPath: "view_path",
            data: new \stdClass(),
            headers: [],
            statusCode: StatusCode::Ok
        );

        $this->assertSame("view_path", $view->viewPath);
        $this->assertIsObject($view->data);
        $this->assertSame(StatusCode::Ok, $view->statusCode);
        $this->assertCount(1, $view->headers);
        $header = $view->headers[0];
        $this->assertInstanceOf(ContentType::class, $header);
        $this->assertTrue($header->equals(ContentType::html()));
    }

    public function testCreateViewWithArrayData(): void
    {
        $view = new View(
            viewPath: "view_path",
            data: ['key' => 'value'],
            headers: [],
            statusCode: StatusCode::Ok
        );

        $this->assertSame("view_path", $view->viewPath);
        $this->assertIsArray($view->data);
        $this->assertSame('value', $view->data['key']);
    }
}
