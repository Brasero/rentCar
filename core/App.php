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

    /**
     * Initialise la liste des modules et enregistre le container de dépendance
     * @param ContainerInterface $container
     * @param array $modules
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->router = $container->get(Router::class);

        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }

        $this->container = $container;
    }

    /**
     * Traite la requete du server en l'envoyant dans la chaine de résponsabilité
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface {
        return $this->middleware->process($request);
    }

    /**
     * Enregistre le premier Middleware de la chaine de résponsabilité
     * @param MiddlewareInterface $middleware
     * @return MiddlewareInterface
     */
    public function linkFirst(MiddlewareInterface $middleware): MiddlewareInterface
    {
        $this->middleware = $middleware;
        return $middleware;
    }

    /**
     * Retourne l'instance de PHP DI
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}