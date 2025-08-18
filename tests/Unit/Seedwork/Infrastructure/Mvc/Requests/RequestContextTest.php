<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Requests;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

final class RequestContextTest extends TestCase
{
    public function testGetReturnsStringValue(): void
    {
        $context = new RequestContext(['foo' => 'bar']);
        $this->assertSame('bar', $context->get('foo'));
    }

    public function testGetThrowsExceptionIfKeyNotFound(): void
    {
        $context = new RequestContext(['foo' => 'bar']);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Key 'baz' not found");
        $context->get('baz');
    }

    public function testGetAsReturnsTypedValue(): void
    {
        $object = new \stdClass();
        $context = new RequestContext(['obj' => $object]);
        $result = $context->getAs('obj', \stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertSame($object, $result);
    }

    public function testGetAsThrowsExceptionIfTypeMismatch(): void
    {
        $context = new RequestContext(['obj' => new \stdClass()]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Value for key 'obj' is not of type 'DateTime'");
        $context->getAs('obj', \DateTime::class);
    }

    public function testGetAsThrowsExceptionIfKeyNotFound(): void
    {
        $context = new RequestContext(['foo' => 'bar']);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Key 'baz' not found");
        $context->getAs('baz', \stdClass::class);
    }

    public function testSetAddsOrUpdatesValue(): void
    {
        $context = new RequestContext(['foo' => 'bar']);
        $context->set('foo', 'baz');
        $this->assertSame('baz', $context->get('foo'));

        $context->set('new', 'value');
        $this->assertSame('value', $context->get('new'));
    }
}
