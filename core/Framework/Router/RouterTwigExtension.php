<?php
namespace Core\Framework\Router;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterTwigExtension extends AbstractExtension {

    private Router $router;
    public function __construct(Router $router){
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'path'])
        ];
    }

    public function path(string $name, array $params = []): string {
        return $this->router->generateUri($name, $params);
    }
}