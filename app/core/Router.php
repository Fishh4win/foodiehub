<?php
namespace App\Core;

class Router {
    private $routes = [];

    /**
     * Register a GET route
     */
    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }

    /**
     * Register a POST route
     */
    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }

    /**
     * Register a PUT route
     */
    public function put($uri, $action) {
        $this->routes['PUT'][$uri] = $action;
    }

    /**
     * Register a DELETE route
     */
    public function delete($uri, $action) {
        $this->routes['DELETE'][$uri] = $action;
    }

    /**
     * Dispatch the request to the appropriate route handler
     */
    public function dispatch($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);

        // Handle trailing slash
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            $this->executeAction($this->routes[$method][$uri]);
            return;
        }

        // Check for routes with parameters
        foreach ($this->routes[$method] as $route => $action) {
            $pattern = $this->convertRouteToRegex($route);

            if (preg_match($pattern, $uri, $matches)) {
                // Remove the full match
                array_shift($matches);

                $this->executeAction($action, $matches);
                return;
            }
        }

        // No route found
        http_response_code(404);
        echo (new View)->render("errors.404");
    }

    /**
     * Convert route pattern to regex
     */
    private function convertRouteToRegex($route) {
        // Replace {param} with regex pattern
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);

        // Escape forward slashes and add start/end markers
        $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';

        return $pattern;
    }

    /**
     * Execute the route action
     */
    private function executeAction($action, $params = []) {
        [$controller, $method] = explode("@", $action);
        $controller = "App\\Controllers\\$controller";

        $controllerInstance = new $controller();

        if (empty($params)) {
            $controllerInstance->$method();
        } else {
            call_user_func_array([$controllerInstance, $method], $params);
        }
    }
}
