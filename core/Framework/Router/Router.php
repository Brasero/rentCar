<?php

namespace Core\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

class Router
{

    private FastRouteRouter $router;

    private array $routes = [];


    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    public function get(string $path, $callable, string $name): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
        $this->routes[] = $name;
    }

    public function post(string $path, $callable, string $name = null): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }

    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        }

        return null;
    }

    public function generateUri(string $name, ?array $params = []): ?string
    {
        return $this->router->generateUri($name, $params);
    }
}