<?php
declare(strict_types=1);

namespace App\Helpers;

abstract class Controller
{
    /**
     * Render a view file inside a layout.
     *
     * @param string $view   e.g. "auth/login" -> src/app/views/auth/login.php
     * @param array  $data
     * @param string|null $layout e.g. "layout/customer-layout"; null = no layout
     */
    protected function view(string $view, array $data = [], ?string $layout = 'layout/customer-layout'): void
    {
        $viewPath = dirname(__DIR__) . '/views/' . $view . '.php';
        if (!is_file($viewPath)) {
            http_response_code(500);
            echo "View not found: {$view}";
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__) . '/views/' . $layout . '.php';
        if (!is_file($layoutPath)) {
            echo $content;
            return;
        }
        include $layoutPath;
    }

    protected function redirect(string $url): void
    {
        if (!preg_match('#^https?://#', $url)) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url);
        exit;
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function requireCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!\App\Helpers\Csrf::verify((string) $token)) {
            http_response_code(419);
            echo 'CSRF token mismatch';
            exit;
        }
    }
}
