<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Application\Messaging;

use Domain\Restaurants\Entities\Restaurant;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Seedwork\Application\Messaging\DomainEventsBus;
use Seedwork\Application\Messaging\EventPublishingRepositoryDecorator;
use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\Repository;

/**
 * @internal
 *
 * @coversNothing
 */
final class EventPublishingRepositoryDecoratorTest extends TestCase
{
    /** @var MockObject&Repository<Restaurant> */
    private MockObject&Repository $inner;
    private DomainEventsBus&Stub $domainEventsBus;
    /** @var EventPublishingRepositoryDecorator<Restaurant> */
    private EventPublishingRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        /** @var MockObject&Repository<Restaurant> $inner */
        $inner = $this->createMock(Repository::class);
        $this->inner = $inner;
        $this->domainEventsBus = $this->createStub(DomainEventsBus::class);
        $this->decorator = new EventPublishingRepositoryDecorator(
            $this->inner,
            $this->domainEventsBus,
        );
    }

    public function testSaveDelegatesToInnerRepository(): void
    {
        $restaurant = Restaurant::new('test@example.com');
        $this->inner->expects($this->once())->method('save')->with($restaurant);

        $this->decorator->save($restaurant);
    }

    public function testSavePublishesEventsFromAggregateToDomainEventsBus(): void
    {
        $restaurant = Restaurant::new('test@example.com');
        $this->inner->expects($this->once())->method('save')->with($restaurant);
        $this->domainEventsBus->method('publish')
            ->with($this->callback(function (DomainEvent $event): bool {
                return 'RestaurantCreated' === $event->type;
            }));

        $this->decorator->save($restaurant);
    }

    public function testGetByIdDelegatesToInner(): void
    {
        $restaurant = Restaurant::new('test@example.com');
        $this->inner
            ->expects($this->once())
            ->method('getById')
            ->with($restaurant->getId())
            ->willReturn($restaurant);

        $result = $this->decorator->getById($restaurant->getId());

        $this->assertSame($restaurant, $result);
    }
}
