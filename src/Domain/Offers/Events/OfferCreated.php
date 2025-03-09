<?php

declare(strict_types=1);

namespace App\Domain\Offers\Events;

use App\Domain\Offers\Entities\Offer;
use Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class OfferCreated extends DomainEvent
{
    public static function new(string $offerId, Offer $offer): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'OfferCreated',
            payload: ['offerId' => $offerId, 'offer' => $offer]
        );
    }

    public static function build(string $offerId, Offer $offer, string $id): self
    {
        return new self(
            id: $id,
            type: 'OfferCreated',
            payload: ['offerId' => $offerId, 'offer' => $offer]
        );
    }
}
