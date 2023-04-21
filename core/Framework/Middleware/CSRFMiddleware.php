<?php

namespace Core\Framework\Middleware;

use Core\Framework\Security\CSRF;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class CSRFMiddleware extends AbstractMiddleware
{

    private CSRF $csrf;
    private array $excludeUrls;

    public function __construct(ContainerInterface $container, array $excludeUrls = [])
    {
        $this->csrf = $container->get(CSRF::class);
        $this->excludeUrls = $excludeUrls;
    }


    public function process(ServerRequestInterface $request)
    {
        $method = $request->getMethod();
        $route = $request->getUri()->getPath();

        if (
            in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])
            &&
            !in_array($route, $this->excludeUrls)
        ) {
            $data = $request->getParsedBody();
            $token = $data[$this->csrf->getFormKey()] ?? null;
            if(!$this->csrf->checkToken($token)) {
                throw new \Exception('Token csrf invalide');
            }
        }

        return parent::process($request);
    }
}