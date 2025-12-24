<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Restaurants\Requests;

final readonly class SelectRestaurantRequest
{
    public function __construct(
        public string $restaurantId,
        public string $backUrl = '/',
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->restaurantId)) {
            $errors['restaurantId'] = '{{restaurants.select.form.restaurantId.error.required}}';
        }

        return $errors;
    }
}
