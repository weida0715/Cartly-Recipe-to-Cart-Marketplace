<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';
require_once __DIR__ . '/../src/app/helper/Model.php';

use App\Models\Product;

class ProductSearchTest extends TestCase
{
    public function test_paginated_product_search_binds_each_like_placeholder(): void
    {
        $product = new class extends Product {
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

        $product->paginateActive('rice');

        $this->assertCount(2, $product->queries);
        foreach ($product->queries as $query) {
            $this->assertStringContainsString('p.product_name LIKE :q_name', $query['sql']);
            $this->assertStringContainsString('p.description LIKE :q_description', $query['sql']);
            $this->assertStringContainsString('i.ingredient_name LIKE :q_ingredient', $query['sql']);
            $this->assertSame('%rice%', $query['params'][':q_name']);
            $this->assertSame('%rice%', $query['params'][':q_description']);
            $this->assertSame('%rice%', $query['params'][':q_ingredient']);
        }
    }

    public function test_active_product_search_uses_distinct_like_placeholders(): void
    {
        $product = new class extends Product {
            public array $lastQuery = [];

            public function query(string $sql, array $params = []): array
            {
                $this->lastQuery = ['sql' => $sql, 'params' => $params];
                return [];
            }
        };

        $product->active('milk');

        $this->assertStringContainsString('p.product_name LIKE :q_name', $product->lastQuery['sql']);
        $this->assertStringContainsString('p.description LIKE :q_description', $product->lastQuery['sql']);
        $this->assertStringContainsString('i.ingredient_name LIKE :q_ingredient', $product->lastQuery['sql']);
        $this->assertSame('%milk%', $product->lastQuery['params'][':q_name']);
        $this->assertSame('%milk%', $product->lastQuery['params'][':q_description']);
        $this->assertSame('%milk%', $product->lastQuery['params'][':q_ingredient']);
    }
}
