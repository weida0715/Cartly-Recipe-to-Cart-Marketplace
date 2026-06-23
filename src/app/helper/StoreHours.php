<?php
declare(strict_types=1);

namespace App\Helpers;

final class StoreHours
{
    public static function normalize(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        $time = \DateTimeImmutable::createFromFormat('!H:i', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        if (
            $time === false
            || $time->format('H:i') !== $value
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        ) {
            return null;
        }
        return $value . ':00';
    }

    public static function error(?string $openingTime, ?string $closingTime): ?string
    {
        $openingRaw = trim((string) $openingTime);
        $closingRaw = trim((string) $closingTime);
        if ($openingRaw === '' || $closingRaw === '') {
            return 'Opening time and closing time are required.';
        }
        $opening = self::normalize($openingRaw);
        $closing = self::normalize($closingRaw);
        if ($opening === null || $closing === null) {
            return 'Enter valid store operating hours.';
        }
        if ($opening === $closing) {
            return 'Opening time and closing time must be different.';
        }
        return null;
    }

    public static function display(?string $value): string
    {
        $timestamp = strtotime((string) $value);
        return $timestamp === false ? 'Not set' : date('g:i A', $timestamp);
    }
}