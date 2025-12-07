<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Places\Requests;

final readonly class UpdatePlaceRequest
{
    public function __construct(
        public string $name = '',
        public int $capacity = 1,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = '{{places.form.name.error.required}}';
        }

        if ($this->capacity <= 0) {
            $errors['capacity'] = '{{places.form.capacity.error.min}}';
        }

        return $errors;
    }
}
