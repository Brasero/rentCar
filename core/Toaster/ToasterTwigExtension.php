<?php
namespace Core\Toaster;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ToasterTwigExtension extends AbstractExtension
{
    private Toaster $toaster;

    public function __construct(ContainerInterface $container)
    {
        $this->toaster = $container->get(Toaster::class);
    }

    public function getFunctions()
    {
        return [
          new TwigFunction('hasToast', [$this, 'hasToast']),
          new TwigFunction('renderToast', [$this, 'render'], ['is_safe' => ['html']])
        ];
    }

    public function hasToast(): bool
    {
        return $this->toaster->hasToast();
    }

    public function render(): array
    {
        return $this->toaster->renderToast();
    }
}