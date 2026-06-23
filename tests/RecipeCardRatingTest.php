<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/uploads');
}

class RecipeCardRatingTest extends TestCase
{
    public function test_recipe_card_displays_average_rating_and_review_count(): void
    {
        $html = $this->renderRecipeIndex([[
            'recipe_id' => 1,
            'recipe_title' => 'Rated Recipe',
            'review_rating' => 4.25,
            'review_count' => 3,
        ]]);

        $this->assertStringContainsString('<strong>4.3</strong>', $html);
        $this->assertStringContainsString('(3 reviews)', $html);
        $this->assertStringContainsString('4.3 out of 5 stars from 3 reviews', $html);
    }

    public function test_recipe_card_displays_singular_review_label(): void
    {
        $html = $this->renderRecipeIndex([[
            'recipe_id' => 2,
            'recipe_title' => 'Single Review',
            'review_rating' => 5,
            'review_count' => 1,
        ]]);

        $this->assertStringContainsString('(1 review)', $html);
        $this->assertStringContainsString('5.0 out of 5 stars from 1 review', $html);
    }

    public function test_recipe_card_displays_unrated_state(): void
    {
        $html = $this->renderRecipeIndex([[
            'recipe_id' => 3,
            'recipe_title' => 'New Recipe',
            'review_rating' => 0,
            'review_count' => 0,
        ]]);

        $this->assertStringContainsString('Not rated yet', $html);
    }

    private function renderRecipeIndex(array $recipes): string
    {
        $q = '';
        $cuisine = '';
        $difficulty = '';
        $sort = 'newest';
        $total = count($recipes);
        $page = 1;
        $pages = 1;
        $_GET = [];

        ob_start();
        require __DIR__ . '/../src/app/views/recipe/index.php';
        return (string) ob_get_clean();
    }
}
