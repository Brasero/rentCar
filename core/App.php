<?php
namespace Core;

use Core\Framework\Middleware\MiddlewareInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Core\Framework\Router\Router;

class App
{
    private Router $router;
    private array $modules;

    private ContainerInterface $container;

    private MiddlewareInterface $middleware;
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->router = $container->get(Router::class);

        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }

        $this->container = $container;
    }

    public function run(ServerRequestInterface $request): ResponseInterface {
        return $this->middleware->process($request);
    }

    public function linkFirst(MiddlewareInterface $middleware): MiddlewareInterface
    {
        $this->middleware = $middleware;
        return $middleware;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}