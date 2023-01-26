<?php
namespace App\Home;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;

class HomeModule
{

    private Router $router;
    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;

        $this->renderer->addPath('home',__DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/', [$this, 'index'], 'accueil');
    }

    public function index()
    {
        return $this->renderer->render('@home/index',
        ['siteName' => 'RentCar']);
    }
}