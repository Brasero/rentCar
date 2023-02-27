<?php
namespace Core\Framework\Router;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension Twig permettant d'appeler des fonction definie du Router a l'intérieur des vues twig
 */
class RouterTwigExtension extends AbstractExtension {

    private Router $router;

    /**
     * Récupère l'instance du Router et l'enregistre
     * @param Router $router
     */
    public function __construct(Router $router){
        $this->router = $router;
    }

    /**
     * Déclare les fonctions disponible côté vue
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'path'])
        ];
    }

    /**
     * Fait appel a la methode generateUri()  du router et retourne son résultat
     * @param string $name
     * @param array $params
     * @return string
     */
    public function path(string $name, array $params = []): string {
        return $this->router->generateUri($name, $params);
    }
}