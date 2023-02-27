<?php
namespace Core\Framework\Renderer;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{
    /**
     * Methode magique qui est appelée au moment ou l'on essaye d'utiliser l'objet comme si il s'agissait d'une fonction
     * Exemple: $twig = TwigRendererFactory()
     * @param ContainerInterface $container
     * @return TwigRenderer|null
     */
    public function __invoke(ContainerInterface $container): ?TwigRenderer
    {
        $loader = new FilesystemLoader($container->get('config.viewPath'));
        $twig = new Environment($loader, []);

        // Récupère la liste d'extensions Twig à charger
        $extensions = $container->get("twig.extensions");
        // Boucle sur la liste d'extension et ajout à Twig
        foreach ($extensions as $extension) {
            $twig->addExtension($container->get($extension));
        }
        return new TwigRenderer($loader, $twig);
    }
}