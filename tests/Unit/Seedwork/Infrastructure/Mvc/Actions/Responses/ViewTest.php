<?php

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Actions\Responses\Views;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Responses\Headers\ContentType;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

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
        $this->assertInstanceOf(\stdClass::class, $view->data);
        $this->assertSame(StatusCode::Ok, $view->statusCode);
        $this->assertCount(1, $view->headers);
        $header = $view->headers[0];
        $this->assertInstanceOf(ContentType::class, $header);
        $this->assertTrue($header->equals(ContentType::html()));
    }
}
