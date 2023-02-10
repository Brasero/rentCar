<?php
namespace App\Car;

use App\Car\Action\CarAction;
use App\Car\Action\MarqueAction;
use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class CarModule extends AbstractModule
{
    private Router $router;
    private RendererInterface $renderer;

    public const DEFINITIONS = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(ContainerInterface $container)
    {
        $this->router = $container->get(Router::class);
        $this->renderer = $container->get(RendererInterface::class);
        $carAction = $container->get(CarAction::class);
        $marqueAction = $container->get(MarqueAction::class);

        $this->renderer->addPath('car',__DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/addCar', [$carAction, 'addCar'], 'car.add');
        $this->router->get('/listCar', [$carAction, 'listCar'], 'car.list');
        $this->router->get('/show/{id:[\d]+}', [$carAction, 'show'], 'car.show');
        $this->router->get('/update/{id:[\d]+}', [$carAction, 'update'], 'car.update');
        $this->router->post('/update/{id:[\d]+}', [$carAction, 'update']);
        $this->router->post('/addCar', [$carAction, 'addCar']);
        $this->router->get('/delete/{id:[\d]+}', [$carAction, 'delete'], 'car.delete');
        $this->router->get('/addMarque', [$marqueAction, 'addMarque'], 'marque.add');
        $this->router->post('/addMarque', [$marqueAction, 'addMarque']);
        $this->router->get('/marqueList', [$marqueAction, 'marqueList'], 'marque.list');
    }
}