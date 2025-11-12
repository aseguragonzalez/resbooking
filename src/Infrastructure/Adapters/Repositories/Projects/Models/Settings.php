<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects\Models;

final readonly class Settings implements \JsonSerializable
{
    public function __construct(
        public string $email,
        public bool $hasReminders,
        public string $name,
        public int $maxNumberOfDiners,
        public int $minNumberOfDiners,
        public int $numberOfTables,
        public string $phone,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'email' => $this->email,
            'hasReminders' => $this->hasReminders,
            'name' => $this->name,
            'maxNumberOfDiners' => $this->maxNumberOfDiners,
            'minNumberOfDiners' => $this->minNumberOfDiners,
            'numberOfTables' => $this->numberOfTables,
            'phone' => $this->phone,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        /** @var string $email */
        $email = $data['email'] ?? '';
        /** @var bool $hasReminders */
        $hasReminders = $data['hasReminders'] ?? false;
        /** @var string $name */
        $name = $data['name'] ?? '';
        /** @var int $maxNumberOfDiners */
        $maxNumberOfDiners = $data['maxNumberOfDiners'] ?? 0;
        /** @var int $minNumberOfDiners */
        $minNumberOfDiners = $data['minNumberOfDiners'] ?? 0;
        /** @var int $numberOfTables */
        $numberOfTables = $data['numberOfTables'] ?? 0;
        /** @var string $phone */
        $phone = $data['phone'] ?? '';
        return new self(
            email: $email,
            hasReminders: $hasReminders,
            name: $name,
            maxNumberOfDiners: $maxNumberOfDiners,
            minNumberOfDiners: $minNumberOfDiners,
            numberOfTables: $numberOfTables,
            phone: $phone,
        );
    }
}
