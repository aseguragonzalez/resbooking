<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain;

use PHPUnit\Framework\TestCase;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyDomainEvent;

class DomainEventTest extends TestCase
{
    public function testIdPropertyReturnsId(): void
    {
        $event = new DummyDomainEvent('my-id');
        $this->assertSame('my-id', $event->id);
    }

    public function testTypePropertyReturnsType(): void
    {
        $event = new DummyDomainEvent('id', 'MyType');
        $this->assertSame('MyType', $event->type);
    }

    public function testVersionPropertyReturnsVersion(): void
    {
        $event = new DummyDomainEvent('id', 'type', '3.0');
        $this->assertSame('3.0', $event->version);
    }

    public function testPayloadPropertyReturnsPayload(): void
    {
        $payload = ['key' => 'value'];
        $event = new DummyDomainEvent('id', 'type', '1.0', $payload);
        $this->assertSame($payload, $event->payload);
    }

    public function testCreatedAtPropertyReturnsDateTime(): void
    {
        $date = new \DateTimeImmutable('2022-10-05T12:00:00Z');
        $event = new DummyDomainEvent('id', 'type', '1.0', [], $date);
        $this->assertEquals($date, $event->createdAt);
    }

    public function testEqualsReturnsTrueForSameId(): void
    {
        $event1 = new DummyDomainEvent('same-id');
        $event2 = new DummyDomainEvent('same-id');
        $this->assertTrue($event1->equals($event2));
    }

    public function testEqualsReturnsFalseForDifferentId(): void
    {
        $event1 = new DummyDomainEvent('id-1');
        $event2 = new DummyDomainEvent('id-2');
        $this->assertFalse($event1->equals($event2));
    }
}
