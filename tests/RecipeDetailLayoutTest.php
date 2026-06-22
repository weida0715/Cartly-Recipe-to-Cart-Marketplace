<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/uploads');
}

class RecipeDetailLayoutTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_recipe_details_render_two_column_summary_and_content_cards(): void
    {
        $html = $this->renderRecipeShow(
            [
                'recipe_id' => 7,
                'recipe_title' => 'Carrot Soup',
                'cuisine_type' => 'Western',
                'difficulty' => 'medium',
                'prep_time' => 10,
                'cook_time' => 25,
                'base_servings' => 2,
                'description' => 'A warming soup.',
                'instructions' => '1. Chop carrots. 2. Simmer until tender. 3. Blend until smooth.',
            ],
            [[
                'ingredient_name' => 'Carrots',
                'quantity' => 500,
                'unit' => 'g',
            ]]
        );

        $this->assertStringContainsString('class="recipe-detail-hero"', $html);
        $this->assertStringContainsString('class="recipe-detail-content"', $html);
        $this->assertStringContainsString('class="card recipe-ingredients-card"', $html);
        $this->assertStringContainsString('class="card recipe-instructions-card"', $html);
        $this->assertSame(3, substr_count($html, '<li><span>'));
    }

    public function test_ingredient_rows_include_serving_scaling_data(): void
    {
        $html = $this->renderRecipeShow(
            [
                'recipe_id' => 8,
                'recipe_title' => 'Rice Bowl',
                'base_servings' => 4,
                'instructions' => "Cook rice.\nServe warm.",
            ],
            [[
                'ingredient_name' => 'Rice',
                'quantity' => 2.5,
                'unit' => 'cups',
            ]]
        );

        $this->assertStringContainsString('data-recipe-servings data-base-servings="4"', $html);
        $this->assertStringContainsString('data-base-quantity="2.5"', $html);
        $this->assertStringContainsString('>2.5</strong>', $html);
    }

    public function test_instruction_parser_keeps_numbers_inside_a_step(): void
    {
        $html = $this->renderRecipeShow([
            'recipe_id' => 9,
            'recipe_title' => 'Measured Recipe',
            'base_servings' => 1,
            'instructions' => 'Add 1. Then stir until combined.',
        ], []);

        $this->assertSame(1, substr_count($html, '<li><span>'));
        $this->assertStringContainsString('Add 1. Then stir until combined.', $html);
    }

    public function test_recipe_details_render_empty_ingredient_and_instruction_states(): void
    {
        $html = $this->renderRecipeShow([
            'recipe_id' => 9,
            'recipe_title' => 'Draft Recipe',
            'base_servings' => 1,
            'instructions' => '',
        ], []);

        $this->assertStringContainsString('No ingredients have been added yet.', $html);
        $this->assertStringContainsString('No cooking instructions have been added yet.', $html);
    }

    public function test_app_javascript_contains_recipe_serving_sync(): void
    {
        $javascript = file_get_contents(__DIR__ . '/../src/public/assets/js/app.js');

        $this->assertIsString($javascript);
        $this->assertStringContainsString('[data-recipe-servings]', $javascript);
        $this->assertStringContainsString('[data-recipe-servings-target]', $javascript);
        $this->assertStringContainsString('[data-ingredient-quantity]', $javascript);
        $this->assertStringContainsString("event?.type === 'input'", $javascript);
        $this->assertStringContainsString('Number.isNaN(parsed)', $javascript);
        $this->assertStringContainsString("event?.type === 'change' || !event", $javascript);
    }

    private function renderRecipeShow(array $recipe, array $ingredients): string
    {
        $reviews = [];
        $isSaved = false;
        $currentUserReview = null;

        ob_start();
        require __DIR__ . '/../src/app/views/recipe/show.php';
        return (string) ob_get_clean();
    }
}
