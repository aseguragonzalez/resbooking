<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests;

final readonly class UpdateDiningAreaRequest
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
            $errors['name'] = '{{dining-areas.form.name.error.required}}';
        }

        if ($this->capacity <= 0) {
            $errors['capacity'] = '{{dining-areas.form.capacity.error.min}}';
        }

        return $errors;
    }
}
