<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/config/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class DeploymentBootstrapTest extends TestCase
{
    public function test_normalize_base_path_supports_empty_and_nested_paths(): void
    {
        $this->assertSame('', cartly_normalize_base_path(''));
        $this->assertSame('', cartly_normalize_base_path('/'));
        $this->assertSame('/pr-14', cartly_normalize_base_path('pr-14'));
        $this->assertSame('/nested/app', cartly_normalize_base_path('/nested/app/'));
    }

    public function test_env_file_loader_populates_process_environment(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'cartly-env-');
        $this->assertNotFalse($tempFile);

        file_put_contents($tempFile, "APP_ENV=preview\nAPP_BASE_PATH=/pr-test\nDB_NAME=cartly_preview_pr_test\n");

        cartly_load_env($tempFile);

        $this->assertSame('preview', cartly_env('APP_ENV'));
        $this->assertSame('/pr-test', cartly_env('APP_BASE_PATH'));
        $this->assertSame('cartly_preview_pr_test', cartly_env('DB_NAME'));

        @unlink($tempFile);
    }
}
