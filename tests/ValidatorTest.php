<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Validator.php';

use App\Helpers\Validator;

class ValidatorTest extends TestCase
{
    public function test_phone_accepts_digits_only(): void
    {
        $validator = new Validator(['phone' => '0123456789']);

        $validator->phone('phone');

        $this->assertFalse($validator->fails());
    }

    public function test_phone_rejects_non_numeric_characters(): void
    {
        $validator = new Validator(['phone' => '01234abc']);

        $validator->phone('phone');

        $this->assertTrue($validator->fails());
        $this->assertSame('Phone number must contain digits only.', $validator->errors['phone']);
    }

    public function test_phone_allows_empty_optional_value(): void
    {
        $validator = new Validator(['phone' => '']);

        $validator->phone('phone');

        $this->assertFalse($validator->fails());
    }

    public function test_required_accepts_non_empty_array_values(): void
    {
        $validator = new Validator(['choices' => ['vegetarian']]);

        $validator->required('choices', 'Choices');

        $this->assertFalse($validator->fails());
    }

    public function test_required_rejects_empty_array_values(): void
    {
        $validator = new Validator(['choices' => []]);

        $validator->required('choices', 'Choices');

        $this->assertTrue($validator->fails());
        $this->assertSame('Choices is required.', $validator->errors['choices']);
    }
}
