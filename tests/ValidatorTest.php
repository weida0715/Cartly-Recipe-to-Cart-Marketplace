<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Validator.php';

use App\Helpers\Validator;

class ValidatorTest extends TestCase
{
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
