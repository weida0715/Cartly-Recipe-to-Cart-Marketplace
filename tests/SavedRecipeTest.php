<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';
require_once __DIR__ . '/../src/app/helper/Model.php';

use App\Models\SavedRecipe;

class SavedRecipeTest extends TestCase
{
    public function test_is_saved_returns_true_when_saved_row_exists(): void
    {
        $savedRecipe = new class extends SavedRecipe {
            public array $lastQuery = [];

            public function query(string $sql, array $params = []): array
            {
                $this->lastQuery = ['sql' => $sql, 'params' => $params];
                return [['saved_id' => 7]];
            }
        };

        $this->assertTrue($savedRecipe->isSaved(3, 12));
        $this->assertStringContainsString('WHERE user_id = :u AND recipe_id = :r', $savedRecipe->lastQuery['sql']);
        $this->assertSame([':u' => 3, ':r' => 12], $savedRecipe->lastQuery['params']);
    }

    public function test_is_saved_returns_false_when_saved_row_is_missing(): void
    {
        $savedRecipe = new class extends SavedRecipe {
            public function query(string $sql, array $params = []): array
            {
                return [];
            }
        };

        $this->assertFalse($savedRecipe->isSaved(3, 12));
    }
}
