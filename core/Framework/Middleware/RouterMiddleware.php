<?php
namespace Core\Framework\Middleware;

use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Vérifie que l'url de la requête corresponde à une route, si oui enregistre la route dans $request, récupère les attributs de la route ($_GET)
 * sinon on passe la requête au middleware suivant sans modification dans le but quelle finisse dans les NotFoundMiddleware
 * P.S.: a besoin du router pour fonctionner
 */
class RouterMiddleware extends AbstractMiddleware
{
    private ContainerInterface $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request)
    {
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if(is_null($route)) {
            return parent::process($request);
        }
        $params = $route->getParams();

        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        $request = $request->withAttribute('_route', $route);

        return parent::process($request);
    }
}