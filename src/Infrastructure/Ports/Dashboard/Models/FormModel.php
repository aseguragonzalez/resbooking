<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models;

abstract class FormModel
{
    public readonly object $errors;

    /**
     * @var array<ErrorModel>
     */
    public readonly array $errorSummary;

    /**
     * @param array<string, string> $errors
     */
    protected function __construct(
        public readonly string $pageTitle,
        array $errors = [],
    ) {
        $this->errors = (object)$errors;
        $summary = [];
        foreach ($errors as $field => $message) {
            $summary[] = new ErrorModel($field, $message);
        }
        $this->errorSummary = $summary;
    }
}
