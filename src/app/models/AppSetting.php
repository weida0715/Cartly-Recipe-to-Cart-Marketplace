<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Model;

class AppSetting extends Model
{
    public const DELIVERY_FEE_KEY = 'delivery_fee_per_store';

    protected string $table = 'application_settings';
    protected string $primaryKey = 'setting_key';

    public function get(string $key, string $default = ''): string
    {
        $row = $this->find($key);
        return $row ? (string) $row['setting_value'] : $default;
    }

    public function getFloat(string $key, float $default = 0.0): float
    {
        $value = $this->get($key, (string) $default);
        return is_numeric($value) ? max(0, (float) $value) : $default;
    }

    public function deliveryFee(): float
    {
        return round($this->getFloat(self::DELIVERY_FEE_KEY, 0.0), 2);
    }

    public function set(string $key, string $value): void
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO application_settings (setting_key, setting_value)
             VALUES (:setting_key, :setting_value)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        $stmt->execute([
            ':setting_key' => $key,
            ':setting_value' => $value,
        ]);
    }
}