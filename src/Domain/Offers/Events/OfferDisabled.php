<?php

declare(strict_types=1);

namespace App\Domain\Offers\Events;

use App\Domain\Offers\Entities\Offer;
use App\Seedwork\Domain\DomainEvent;
use Tuupola\Ksuid;

final class OfferDisabled extends DomainEvent
{
    public static function new(string $offerId, Offer $offer): self
    {
        return new self(
            id: (string)new Ksuid(),
            type: 'OfferDisabled',
            payload: ['offerId' => $offerId, 'offer' => $offer]
        );
    }

    public static function build(string $offerId, Offer $offer, string $id): self
    {
        return new self(
            id: $id,
            type: 'OfferDisabled',
            payload: ['offerId' => $offerId, 'offer' => $offer]
        );
    }
}
