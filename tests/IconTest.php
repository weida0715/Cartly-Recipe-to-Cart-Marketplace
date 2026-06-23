<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

use App\Helpers\Icon;

if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/uploads');
}

class IconTest extends TestCase
{
    public function test_store_logo_renders_generated_upload_path(): void
    {
        $html = Icon::storeLogo([
            'store_id' => 1,
            'store_name' => 'Green Valley',
            'store_logo' => 'stores/logos/0123456789abcdef0123456789abcdef.png',
        ]);

        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString(
            '/uploads/stores/logos/0123456789abcdef0123456789abcdef.png',
            $html
        );
    }

    public function test_store_logo_rejects_javascript_url_and_falls_back_to_initials(): void
    {
        $html = Icon::storeLogo([
            'store_id' => 2,
            'store_name' => 'Green Valley',
            'store_logo' => 'javascript:alert(1)',
        ]);

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringContainsString('>GV</span>', $html);
    }

    public function test_store_logo_rejects_path_traversal(): void
    {
        $html = Icon::storeLogo([
            'store_id' => 3,
            'store_name' => 'Farm Lane',
            'store_logo' => 'stores/logos/../../outside.png',
        ]);

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('>FL</span>', $html);
    }

    public function test_store_logo_without_upload_uses_initials(): void
    {
        $html = Icon::storeLogo([
            'store_id' => -2,
            'store_name' => 'Épicerie Marché',
            'store_logo' => null,
        ]);

        $this->assertStringContainsString('store-logo-tone-3', $html);
        $this->assertStringContainsString('>ÉM</span>', $html);
    }
}
