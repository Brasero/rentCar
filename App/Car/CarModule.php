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

/**
 * @inheritDoc
 */
class CarModule extends AbstractModule
{
    private Router $router;
    private RendererInterface $renderer;

    public const DEFINITIONS = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';


    /**
     * Déclare les routes et les methodes disponible pour ce module, definie le chemin vers le dossier de vues du module,
     * définie éventuellement des variables global a toutes les vues
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        //Router pour déclarer les routes
        $this->router = $container->get(Router::class);
        //Renderer pour déclarer les vues
        $this->renderer = $container->get(RendererInterface::class);

        //Ensembles d'action possible
        $carAction = $container->get(CarAction::class);
        $marqueAction = $container->get(MarqueAction::class);

        //Declaration du chemin des vue sous le namespace 'car'
        $this->renderer->addPath('car',__DIR__ . DIRECTORY_SEPARATOR . 'view');

        //Déclaration des routes disponibles en method GET
        $this->router->get('/admin/addCar', [$carAction, 'addCar'], 'car.add');
        $this->router->get('/admin/listCar', [$carAction, 'listCar'], 'car.list');
        $this->router->get('/show/{id:[\d]+}', [$carAction, 'show'], 'car.show');
        $this->router->get('/admin/update/{id:[\d]+}', [$carAction, 'update'], 'car.update');
        $this->router->get('/admin/delete/{id:[\d]+}', [$carAction, 'delete'], 'car.delete');
        $this->router->get('/admin/addMarque', [$marqueAction, 'addMarque'], 'marque.add');
        $this->router->get('/admin/marqueList', [$marqueAction, 'marqueList'], 'marque.list');
        $this->router->get('/admin/delete/marque/{id:[\d]+}', [$marqueAction, 'delete'], 'marque.delete');
        $this->router->get('/admin/updateMarque/{id:[\d]+}', [$marqueAction, 'update'], 'marque.update');

        //Déclaration des routes disponibles en method POST
        $this->router->post('/admin/updateMarque/{id:[\d]+}', [$marqueAction, 'update']);
        $this->router->post('/admin/update/{id:[\d]+}', [$carAction, 'update']);
        $this->router->post('/admin/addCar', [$carAction, 'addCar']);
        $this->router->post('/admin/addMarque', [$marqueAction, 'addMarque']);
    }
}