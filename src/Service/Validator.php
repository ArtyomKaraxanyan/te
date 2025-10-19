<?php

namespace App\Service;

class Validator
{
    public static function sanitizeString(string $input, int $maxLength = 255): string
    {
        $sanitized = strip_tags(trim($input));
        return mb_substr($sanitized, 0, $maxLength);
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePositiveFloat(float $value): bool
    {
        return $value > 0;
    }

    public static function validateId(int $id): bool
    {
        return $id > 0;
    }

    public static function sanitizeHtml(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
