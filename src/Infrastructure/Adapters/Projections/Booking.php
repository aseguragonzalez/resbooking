<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Booking
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $turnId,
        public ?ClientDTO $client,
        public ?string $date,
        public int $diners,
        public string $clientName,
        public string $email,
        public string $phone,
        public ?string $createDate,
        public ?string $state,
        public ?int $offerId,
        public ?int $placeId,
        public string $comment,
        public ?int $bookingSourceId,
        public string $notes,
        public string $preOrder,
        public int $turnSlotId,
        public string $turnStart,
        public string $turnEnd,
        public string $placeName,
        public string $placeDescription,
        public int $placeSize,
        public string $offerTitle,
        public string $offerDescription,
        public string $offerTerms,
        public string $offerStart,
        public string $offerEnd,
        public string $sourceName,
        public string $sourceDescription,
        public int $clientCount,
        public string $table,
        public string $clientComments,
    ) { }
}
