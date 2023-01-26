<?php
namespace App\Car;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class CarModule
{
    private Router $router;
    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;

        $this->renderer->addPath('car',__DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/addCar', [$this, 'addCar'], 'car.addCar');
        $this->router->post('/addCar', [$this, 'saveCar']);
    }

    public function addCar(ServerRequestInterface $request): string
    {
        return $this->renderer->render('@car/addCar');
    }

    public function saveCar(ServerRequestInterface $request): string {
        $data = $request->getParsedBody();

        var_dump($data);

        return $this->renderer->render('@car/addCar');
    }
}