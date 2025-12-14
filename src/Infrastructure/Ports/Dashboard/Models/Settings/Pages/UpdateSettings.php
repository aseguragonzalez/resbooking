<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Settings\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;
use Infrastructure\Ports\Dashboard\Models\Settings\Requests\UpdateSettingsRequest;

final readonly class UpdateSettings extends FormModel
{
    /**
     * @param array<string, string> $errors
     */
    private function __construct(public UpdateSettingsRequest $settings, array $errors = [])
    {
        parent::__construct(pageTitle: '{{restaurants.settings.form.title}}', errors: $errors);
    }

    public static function new(
        string $email,
        bool $hasReminders,
        string $name,
        int $maxNumberOfDiners,
        int $minNumberOfDiners,
        int $numberOfTables,
        string $phone,
    ): self {
        return new self(settings: new UpdateSettingsRequest(
            email: $email,
            hasReminders: $hasReminders ? 'on' : 'off',
            name: $name,
            maxNumberOfDiners: $maxNumberOfDiners,
            minNumberOfDiners: $minNumberOfDiners,
            numberOfTables: $numberOfTables,
            phone: $phone,
        ), errors: []);
    }

    /**
     * @param array<string, string> $errors
     */
    public static function withErrors(UpdateSettingsRequest $request, array $errors): self
    {
        return new self(settings: $request, errors: $errors);
    }
}
