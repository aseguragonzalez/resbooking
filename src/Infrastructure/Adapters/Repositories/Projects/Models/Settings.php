<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects\Models;

final readonly class Settings implements \JsonSerializable
{
    public function __construct(
        public readonly string $email,
        public readonly bool $hasRemainders,
        public readonly string $name,
        public readonly int $maxNumberOfDiners,
        public readonly int $minNumberOfDiners,
        public readonly int $numberOfTables,
        public readonly string $phone,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'email' => $this->email,
            'hasRemainders' => $this->hasRemainders,
            'name' => $this->name,
            'maxNumberOfDiners' => $this->maxNumberOfDiners,
            'minNumberOfDiners' => $this->minNumberOfDiners,
            'numberOfTables' => $this->numberOfTables,
            'phone' => $this->phone,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            hasRemainders: $data['hasRemainders'],
            name: $data['name'],
            maxNumberOfDiners: $data['maxNumberOfDiners'],
            minNumberOfDiners: $data['minNumberOfDiners'],
            numberOfTables: $data['numberOfTables'],
            phone: $data['phone'],
        );
    }
}
