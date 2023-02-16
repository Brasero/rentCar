<?php
namespace Core\Framework\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsTwigExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('assets', [$this, 'asset'])
        ];
    }

    public function asset(string $path): string
    {
        $file = dirname(__DIR__, 3) . '/public/' . $path;
        if (!file_exists($file)) {
            throw new \Exception("Le fichier $file n'existe pas.");
        }
        $path .= '?' . filemtime($file);
        return $path;
    }
}