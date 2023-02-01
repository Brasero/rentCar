<?php
namespace Core\Framework\Renderer;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container): ?TwigRenderer
    {
        $loader = new FilesystemLoader($container->get('config.viewPath'));
        $twig = new Environment($loader, []);

        return new TwigRenderer($loader, $twig);
    }
}