<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Projects\Pages;

use Infrastructure\Ports\Dashboard\Models\FormModel;
use Infrastructure\Ports\Dashboard\Models\Projects\Requests\UpdateSettingsRequest;

final class UpdateSettings extends FormModel
{
    /**
     * @param array<string, string> $errors
     */
    protected function __construct(public readonly UpdateSettingsRequest $settings, array $errors = [])
    {
        parent::__construct(pageTitle: '{{projects.settings.form.title}}', errors: $errors);
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
