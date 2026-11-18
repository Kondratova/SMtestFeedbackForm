<?php

declare(strict_types=1);

namespace App\Acme\Core;

use App\Acme\Feedback\FeedbackController;

final class Router
{
    private array $routes = [];

    function __construct()
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $this->get('/', static function (): void {
            (new FeedbackController())->index();
        });

        $this->post('/feedback', static function (): void {
            (new FeedbackController())->store();
        });

        $this->post('/feedback/list', static function (): void {
            (new FeedbackController())->list();
        });
    }

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $path = parse_url($path, PHP_URL_PATH);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        call_user_func($handler);
    }
}