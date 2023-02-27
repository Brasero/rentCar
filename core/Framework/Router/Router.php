<?php

namespace Core\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

class Router
{

    private FastRouteRouter $router;

    private array $routes = [];


    /**
     * Instancie un FastRouteRouter et l'enregistre
     */
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * Ajoute une route disponible en method GET
     * @param string $path
     * @param $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, $callable, string $name): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
        $this->routes[] = $name;
    }

    /**
     * Ajoute une route disponible en methode POST
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @return void
     */
    public function post(string $path, $callable, string $name = null): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }

    /**
     * Verifie que l'url et la methode de la requete correspondent à une route connue
     * si oui, retourne un objet Route qui correspond
     * @param ServerRequestInterface $request
     * @return Route|null
     */
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

    /**
     * Genere l'url de la route demandée en fonction de son nom
     * [Optionnel] : On peut ajouter un tableau de paramètre
     * @param string $name
     * @param array|null $params [Optionnel]
     * @return string|null
     */
    public function generateUri(string $name, ?array $params = []): ?string
    {
        return $this->router->generateUri($name, $params);
    }
}