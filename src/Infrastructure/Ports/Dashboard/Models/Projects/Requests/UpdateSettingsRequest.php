<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Projects\Requests;

final readonly class UpdateSettingsRequest
{
    public function __construct(
        public string $email,
        public string $name,
        public int $maxNumberOfDiners,
        public int $minNumberOfDiners,
        public int $numberOfTables,
        public string $phone,
        public string $hasReminders = 'off',
    ) {
    }

    public function hasRemindersChecked(): bool
    {
        return $this->hasReminders === 'on';
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->email)) {
            $errors['email'] = '{{projects.settings.form.email.error.required}}';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = '{{projects.settings.form.email.error.invalid}}';
        }

        if (empty($this->name)) {
            $errors['name'] = '{{projects.settings.form.name.error.required}}';
        }

        if (empty($this->phone)) {
            $errors['phone'] = '{{projects.settings.form.phone.error.required}}';
        }

        if ($this->minNumberOfDiners == null) {
            $errors['minNumberOfDiners'] = '{{projects.settings.form.min-number-of-diners.error.required}}';
        } elseif ($this->minNumberOfDiners <= 0) {
            $errors['minNumberOfDiners'] = '{{projects.settings.form.min-number-of-diners.error.negative}}';
        }

        if ($this->maxNumberOfDiners == null) {
            $errors['maxNumberOfDiners'] = '{{projects.settings.form.max-number-of-diners.error.required}}';
        } elseif ($this->maxNumberOfDiners <= 0) {
            $errors['maxNumberOfDiners'] = '{{projects.settings.form.max-number-of-diners.error.negative}}';
        }

        if ($this->numberOfTables == null) {
            $errors['numberOfTables'] = '{{projects.settings.form.number-of-tables.error.required}}';
        } elseif ($this->numberOfTables <= 0) {
            $errors['numberOfTables'] = '{{projects.settings.form.number-of-tables.error.negative}}';
        }

        if (
            !isset($errors['minNumberOfDiners']) &&
            !isset($errors['maxNumberOfDiners']) &&
            $this->minNumberOfDiners > $this->maxNumberOfDiners
        ) {
            $errors['minNumberOfDiners'] = '{{projects.settings.form.min-number-of-diners.error.greater-than-max}}';
        }

        return $errors;
    }
}
