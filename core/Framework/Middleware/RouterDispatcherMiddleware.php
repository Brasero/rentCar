<?php
namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Si une route a été matché, appel la fonction liée a la route
 */
class RouterDispatcherMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request)
    {
        $route = $request->getAttribute('_route');

        if (is_null($route)) {
            return parent::process($request);
        }

        $callback = $route->getCallback();

        $response = call_user_func_array($callback, [$request]);

        if ($response instanceof ResponseInterface) {
            return $response;
        } elseif (is_string($response)) {
            return new Response(200,[], $response);
        } else {
            throw new \Exception("Réponse du server invalide");
        }
    }
}