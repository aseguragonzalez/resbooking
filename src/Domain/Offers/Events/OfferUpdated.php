<?php

declare(strict_types=1);

namespace Domain\Offers\Events;

use Domain\Offers\Entities\Offer;
use Seedwork\Domain\DomainEvent;

final class OfferUpdated extends DomainEvent
{
    public static function new(string $offerId, Offer $offer): self
    {
        return new self(
            id: uniqid(),
            type: 'OfferUpdated',
            payload: ['offerId' => $offerId, 'offer' => $offer]
        );
    }

    public static function build(string $offerId, Offer $offer, string $id): self
    {
        return new self(
            id: $id,
            type: 'OfferUpdated',
            payload: ['offerId' => $offerId, 'offer' => $offer]
        );
    }
}
