<?php
declare(strict_types=1);

namespace App\Helpers;

final class VoucherDateValidator
{
    public static function error(?string $startDate, ?string $endDate, bool $noExpiry): ?string
    {
        if ($noExpiry) {
            return null;
        }
        if (
            $startDate === null
            || $endDate === null
            || trim($startDate) === ''
            || trim($endDate) === ''
        ) {
            return 'Start date and end date are required unless no expiry date is checked.';
        }

        $start = self::parse($startDate);
        $end = self::parse($endDate);
        if ($start === null || $end === null) {
            return 'Voucher dates are invalid.';
        }
        if ($start > $end) {
            return 'End date must be after or equal to start date.';
        }
        return null;
    }

    private static function parse(string $value): ?\DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        if (
            $date === false
            || $date->format('Y-m-d') !== $value
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        ) {
            return null;
        }
        return $date;
    }
}
