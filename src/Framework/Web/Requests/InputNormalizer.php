<?php

declare(strict_types=1);

namespace Framework\Requests;

/**
 * Centralized helpers to normalize and coerce raw request input values.
 *
 * This class is intentionally framework-only (no domain coupling) and kept
 * conservative to avoid surprising behavior. It focuses on:
 * - Normalizing scalar values (trim, normalize newlines)
 * - Providing predictable coercion for common scalar types
 *
 * Failure semantics:
 * - For numeric types, invalid input returns null instead of performing
 *   lossy casts (e.g. "abc" -> 0). Callers decide whether null is allowed.
 * - For booleans, we keep a permissive interpretation compatible with HTML
 *   forms where any non-empty value means true.
 */
final class InputNormalizer
{
    public static function normalizeScalar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_scalar($value)) {
            return null;
        }

        $string = (string) $value;
        $string = str_replace(["\r\n", "\r"], "\n", $string);

        return trim($string);
    }

    public static function toInt(mixed $value): ?int
    {
        $normalized = self::normalizeScalar($value);
        if ($normalized === null || $normalized === '') {
            return null;
        }

        $filtered = filter_var($normalized, FILTER_VALIDATE_INT);

        return $filtered === false ? null : $filtered;
    }

    public static function toFloat(mixed $value): ?float
    {
        $normalized = self::normalizeScalar($value);
        if ($normalized === null || $normalized === '') {
            return null;
        }

        $filtered = filter_var($normalized, FILTER_VALIDATE_FLOAT);

        return $filtered === false ? null : $filtered;
    }

    public static function toString(mixed $value): ?string
    {
        $normalized = self::normalizeScalar($value);

        return $normalized === null ? null : $normalized;
    }

    public static function toBool(mixed $value): bool
    {
        $normalized = self::normalizeScalar($value);
        if ($normalized === null) {
            return false;
        }

        if ($normalized === '' || strtolower($normalized) === 'false' || $normalized === '0') {
            return false;
        }

        return true;
    }
}
