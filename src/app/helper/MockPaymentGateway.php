<?php
declare(strict_types=1);

namespace App\Helpers;

class MockPaymentGateway
{
    private const BANKS = ['maybank', 'cimb', 'public_bank', 'rhb'];
    private const EWALLETS = ['touch_n_go', 'grabpay', 'boost'];

    public function process(string $method, array $input, float $amount): array
    {
        if ($amount < 0) {
            throw new \RuntimeException('Payment amount cannot be negative.');
        }
        if ($amount === 0.0) {
            return $this->approved($method, 'Voucher-covered order', 'Cartly customer', 'No charge required', 0.0);
        }

        return match ($method) {
            'card' => $this->processCard($input, $amount),
            'online_banking' => $this->processBanking($input, $amount),
            'ewallet' => $this->processEwallet($input, $amount),
            default => throw new \RuntimeException('Choose a valid payment method.'),
        };
    }

    private function processCard(array $input, float $amount): array
    {
        $name = trim($this->scalarString($input, 'cardholder_name'));
        $number = preg_replace('/\D+/', '', $this->scalarString($input, 'card_number')) ?? '';
        $expiry = trim($this->scalarString($input, 'card_expiry'));
        $cvv = trim($this->scalarString($input, 'card_cvv'));

        if ($name === '' || strlen($name) > 100) {
            throw new \RuntimeException('Enter the cardholder name.');
        }
        if (!preg_match('/^\d{13,19}$/', $number) || !$this->passesLuhn($number)) {
            throw new \RuntimeException('Enter a valid mock card number.');
        }
        if (!$this->validExpiry($expiry)) {
            throw new \RuntimeException('Enter a valid future expiry date in MM/YY format.');
        }
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            throw new \RuntimeException('Enter a valid CVV.');
        }
        if ($number === '4000000000000002' || str_ends_with($number, '0000')) {
            throw new \RuntimeException('The mock card payment was declined.');
        }

        return $this->approved('card', 'Mock Visa/Mastercard', $name, 'Card ending ' . substr($number, -4), $amount);
    }

    private function processBanking(array $input, float $amount): array
    {
        $name = trim($this->scalarString($input, 'bank_account_name'));
        $bank = $this->scalarString($input, 'bank_name');
        if ($name === '' || strlen($name) > 100 || !in_array($bank, self::BANKS, true)) {
            throw new \RuntimeException('Choose a supported bank and enter the account holder name.');
        }

        return $this->approved(
            'online_banking',
            ucwords(str_replace('_', ' ', $bank)) . ' Mock FPX',
            $name,
            'FPX authenticated account',
            $amount
        );
    }

    private function processEwallet(array $input, float $amount): array
    {
        $name = trim($this->scalarString($input, 'ewallet_name'));
        $provider = $this->scalarString($input, 'ewallet_provider');
        $phone = preg_replace('/\D+/', '', $this->scalarString($input, 'ewallet_phone')) ?? '';
        if ($name === '' || strlen($name) > 100 || !in_array($provider, self::EWALLETS, true)
            || !preg_match('/^\d{9,12}$/', $phone)) {
            throw new \RuntimeException('Enter valid mock e-wallet details.');
        }

        return $this->approved(
            'ewallet',
            ucwords(str_replace('_', ' ', $provider)),
            $name,
            'Wallet phone ending ' . substr($phone, -4),
            $amount
        );
    }

    private function approved(string $method, string $provider, string $payer, string $maskedAccount, float $amount): array
    {
        return [
            'payment_method' => $method,
            'provider_name' => $provider,
            'payer_name' => $payer,
            'masked_account' => $maskedAccount,
            'amount' => round($amount, 2),
            'status' => 'approved',
            'transaction_reference' => 'PAY-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(4))),
        ];
    }

    private function passesLuhn(string $number): bool
    {
        $sum = 0;
        $alternate = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int) $number[$i];
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
            $alternate = !$alternate;
        }
        return $sum % 10 === 0;
    }

    private function validExpiry(string $expiry): bool
    {
        if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiry, $matches)) {
            return false;
        }
        $month = (int) $matches[1];
        $year = 2000 + (int) $matches[2];
        $expiresAt = mktime(23, 59, 59, $month + 1, 0, $year);
        return $expiresAt !== false && $expiresAt >= time();
    }

    private function scalarString(array $input, string $key): string
    {
        $value = $input[$key] ?? null;
        return is_scalar($value) ? (string) $value : '';
    }
}
