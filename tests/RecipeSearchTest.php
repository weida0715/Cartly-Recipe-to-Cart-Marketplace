<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';
require_once __DIR__ . '/../src/app/helper/Model.php';

use App\Models\Recipe;

class RecipeSearchTest extends TestCase
{
    public function test_paginated_recipe_search_binds_distinct_recipe_text_placeholders(): void
    {
        $recipe = new class extends Recipe {
            public array $queries = [];

            public function query(string $sql, array $params = []): array
            {
                $this->queries[] = ['sql' => $sql, 'params' => $params];
                if (str_contains($sql, 'COUNT(*)')) {
                    return [['cnt' => 0]];
                }
                return [];
            }
        };

        $recipe->paginateActive('rice');

        $this->assertCount(2, $recipe->queries);
        foreach ($recipe->queries as $query) {
            $this->assertStringContainsString('r.recipe_title LIKE :q_title', $query['sql']);
            $this->assertStringContainsString('r.description LIKE :q_description', $query['sql']);
            $this->assertStringNotContainsString('r.cuisine_type LIKE', $query['sql']);
            $this->assertSame('%rice%', $query['params'][':q_title']);
            $this->assertSame('%rice%', $query['params'][':q_description']);
        }
    }

    public function test_active_recipe_search_uses_recipe_text_only(): void
    {
        $recipe = new class extends Recipe {
            public array $lastQuery = [];

            public function query(string $sql, array $params = []): array
            {
                $this->lastQuery = ['sql' => $sql, 'params' => $params];
                return [];
            }
        };

        $recipe->active('soup');

        $this->assertStringContainsString('r.recipe_title LIKE :q_title', $recipe->lastQuery['sql']);
        $this->assertStringContainsString('r.description LIKE :q_description', $recipe->lastQuery['sql']);
        $this->assertStringNotContainsString('r.cuisine_type LIKE', $recipe->lastQuery['sql']);
        $this->assertSame('%soup%', $recipe->lastQuery['params'][':q_title']);
        $this->assertSame('%soup%', $recipe->lastQuery['params'][':q_description']);
    }
}
