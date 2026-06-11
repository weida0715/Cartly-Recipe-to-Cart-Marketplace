<?php
declare(strict_types=1);

namespace App\Helpers;

class Validator
{
    public array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = null): self
    {
        $label = $label ?: $field;
        if (!isset($this->data[$field]) || trim((string) $this->data[$field]) === '') {
            $this->errors[$field] = "{$label} is required.";
        }
        return $this;
    }

    public function email(string $field): self
    {
        $v = $this->data[$field] ?? '';
        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Please enter a valid email.';
        }
        return $this;
    }

    public function min(string $field, int $len): self
    {
        $v = (string) ($this->data[$field] ?? '');
        if ($v !== '' && strlen($v) < $len) {
            $this->errors[$field] = "Minimum {$len} characters.";
        }
        return $this;
    }

    public function matches(string $a, string $b, string $msg = 'Values do not match.'): self
    {
        if (($this->data[$a] ?? null) !== ($this->data[$b] ?? null)) {
            $this->errors[$b] = $msg;
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }
}
