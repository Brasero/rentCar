<?php
namespace Core;

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
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->router = $container->get(Router::class);

        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }

        $this->container = $container;
    }

    public function run(ServerRequestInterface $request): ResponseInterface {
        $uri = $request->getUri()->getPath();
        if(!empty($uri) && $uri[-1] === '/' && $uri != '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }

        $route = $this->router->match($request);

        if (is_null($route)) {
            return new Response(404, [], "<h2>Cette page n'existe pas</h2>");
        }

        $response = call_user_func_array($route->getCallback(), [$request]);

        if ($response instanceof ResponseInterface) {
            return $response;
        } elseif (is_string($response)) {
            return new Response(200,[], $response);
        } else {
            throw new Exception("Réponse du server invalide");
        }
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}