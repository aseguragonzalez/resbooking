<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Request
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $turnId,
        public int $clientId,
        public string $date,
        public int $diners,
        public string $clientName,
        public string $email,
        public string $phone,
        public string $createDate,
        public ?int $state,
        public ?int $offerId,
        public ?int $placeId,
        public string $comment,
        public ?int $bookingSourceId,
        public int $turnId,
        public int $turnSlotId,
        public string $turnStart,
        public string $turnEnd,
        public int $placeId,
        public int $placeProjectId,
        public string $placeName,
        public string $placeDescription,
        public int $placeSize,
        public bool $placeActive,
        public int $offerId,
        public int $offerProjectId,
        public string $offerTitle,
        public string $offerDescription,
        public string $offerTerms,
        public string $offerStart,
        public string $offerEnd,
        public bool $offerActive,
    ) {
    }
}
