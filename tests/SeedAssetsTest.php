<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/database/seed_assets.php';

use PHPUnit\Framework\TestCase;

final class SeedAssetsTest extends TestCase
{
    public function testManifestContainsExpectedSeedGroups(): void
    {
        $manifest = cartly_seed_asset_manifest();

        $this->assertArrayHasKey('categories', $manifest);
        $this->assertArrayHasKey('products', $manifest);
        $this->assertArrayHasKey('recipes', $manifest);
        $this->assertCount(7, $manifest['categories']);
        $this->assertCount(13, $manifest['products']);
        $this->assertCount(5, $manifest['recipes']);
    }

    public function testManifestUsesRelativePngPathsOnly(): void
    {
        foreach (cartly_seed_asset_manifest() as $assets) {
            foreach ($assets as $asset) {
                $this->assertStringEndsWith('.png', $asset['source']);
                $this->assertStringEndsWith('.png', $asset['target']);
                $this->assertStringNotContainsString('..', $asset['source']);
                $this->assertStringNotContainsString('..', $asset['target']);
                $this->assertStringStartsWith('seeded/', $asset['target']);
            }
        }
    }

    public function testManifestSourceFilesExistInDatabaseStockImagesFolder(): void
    {
        $stockRoot = cartly_seed_asset_stock_root();

        foreach (cartly_seed_asset_manifest() as $assets) {
            foreach ($assets as $asset) {
                $path = $stockRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $asset['source']);

                $this->assertFileExists($path);
            }
        }
    }
}
