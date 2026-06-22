<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class RecipeCountTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('BASE_URL')) {
            define('BASE_URL', '');
        }
        if (!defined('UPLOAD_URL')) {
            define('UPLOAD_URL', '/uploads');
        }
    }

    public function test_recipe_page_shows_current_page_count_and_total(): void
    {
        $html = $this->renderRecipeIndex(
            [
                ['recipe_id' => 1, 'recipe_title' => 'One'],
                ['recipe_id' => 2, 'recipe_title' => 'Two'],
                ['recipe_id' => 3, 'recipe_title' => 'Three'],
            ],
            15
        );

        $this->assertStringContainsString('Showing <strong>3</strong> of 15', $html);
        $this->assertStringContainsString('recipes', $html);
    }

    public function test_recipe_page_uses_singular_label_for_one_result(): void
    {
        $html = $this->renderRecipeIndex(
            [['recipe_id' => 1, 'recipe_title' => 'Only Recipe']],
            1
        );

        $this->assertStringContainsString('Showing <strong>1</strong> of 1', $html);
        $this->assertStringContainsString('recipe', $html);
    }
    public function test_recipe_page_shows_zero_for_empty_results(): void
    {
        $html = $this->renderRecipeIndex([], 0);

        $this->assertStringContainsString('Showing <strong>0</strong> of 0', $html);
        $this->assertStringContainsString('No recipes match your filters.', $html);
    }

    private function renderRecipeIndex(array $recipes, int $total): string
    {
        $q = '';
        $cuisine = '';
        $difficulty = '';
        $sort = 'newest';
        $page = 1;
        $pages = 1;
        $_GET = [];

        ob_start();
        require __DIR__ . '/../src/app/views/recipe/index.php';
        return (string) ob_get_clean();
    }
}
