<?php

namespace Core\Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{

    private $twig;
    private $loader;

    /**
     * S'attend a une une instance de FilesystemLoader et d'Environment
     * @param FilesystemLoader $loader Objet qui rescense les chemins vers les différents dossier de vus
     * @param Environment $twig Objet qui enregistre nos extensions et permet de faire communiquer vue et controller
     */
    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    /**
     * Permet d'enregistrer un chemin vers un ensemble de vues
     * @param string $namespace Si $path est définie $namespace représente un alias du chemin vers les vues,
     * sinon contient simplement le chemin
     * @param string|null $path Si définie contient le chemin vers les vues qui seront enregistrer sous la valeur de $namespace
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Affiche la vue demandée
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.html.twig', $params);
    }

    /**
     * Ajoute des variable global commune à toutes les vues
     * @param string $key
     * @param $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}