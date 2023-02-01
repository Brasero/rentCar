<?php
namespace App\Car;

use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class CarModule extends AbstractModule
{
    private Router $router;
    private RendererInterface $renderer;

    public const DEFINITIONS = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;

        $this->renderer->addPath('car',__DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/addCar', [$this, 'addCar'], 'car.add');
        $this->router->get('/listCar', [$this, 'listCar'], 'car.list');
        $this->router->get('/show', [$this, 'show'], 'car.show');
        $this->router->post('/addCar', [$this, 'saveCar']);
    }

    public function addCar(ServerRequestInterface $request): string
    {
        return $this->renderer->render('@car/addCar');
    }

    public function saveCar(ServerRequestInterface $request): string {
        $data = $request->getParsedBody();

        return $this->renderer->render('@car/addCar');
    }

    public function listCar(ServerRequestInterface $request): string {
        $voitures = [
            [
                "model" => "206",
                "marque" => "Peugeot",
                "couleur" => "Bleu"
            ],
            [
                "model" => "Golf",
                "marque" => "Volkswagen",
                "couleur" => "Vert"
            ]
        ];

        return $this->renderer->render('@car/list', [
            "voitures" => $voitures
        ]);
    }

    public function show(ServerRequestInterface $request): string {

        $voiture = [
            "model" => "206",
            "marque" => "Peugeot",
            "couleur" => "Bleu"
        ];

        return $this->renderer->render('@car/show', [
            "voiture" => $voiture
        ]);
    }
}