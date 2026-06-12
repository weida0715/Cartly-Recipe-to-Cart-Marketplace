<?php
declare(strict_types=1);

namespace App\Helpers;

class Router
{
    /** @var array<string, array<string, array{handler:callable|array, middleware:array}>> */
    private array $routes = ['GET' => [], 'POST' => []];
    private $notFound = null;

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->routes['GET'][$this->norm($path)] = compact('handler', 'middleware');
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->routes['POST'][$this->norm($path)] = compact('handler', 'middleware');
    }

    public function notFound(callable $fn): void
    {
        $this->notFound = $fn;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = $this->norm($uri);
        $method = strtoupper($method);
        $table = $this->routes[$method] ?? [];

        foreach ($table as $pattern => $route) {
            $regex = $this->compile($pattern);
            if (preg_match($regex, $uri, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    if (is_callable($mw)) {
                        $mw();
                    }
                }
                $this->call($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        if ($this->notFound) {
            ($this->notFound)();
        } else {
            echo '404 Not Found';
        }
    }

    private function call($handler, array $params): void
    {
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = new $class();
            $instance->{$method}(...array_values($params));
            return;
        }
        if (is_callable($handler)) {
            $handler(...array_values($params));
            return;
        }
        throw new \RuntimeException('Invalid route handler');
    }

    private function compile(string $pattern): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    private function norm(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
