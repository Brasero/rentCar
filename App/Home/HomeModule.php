<?php
namespace App\Home;

use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\ServerRequest;
use Model\Entity\Vehicule;

class HomeModule extends AbstractModule
{

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    private EntityManager $manager;

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->manager = $manager;

        $this->renderer->addGlobal('siteName', 'RentCar');
        $this->renderer->addPath('home',__DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/', [$this, 'index'], 'accueil');
        $this->router->get('/list', [$this, 'list'], 'list');
    }

    public function index()
    {
        return $this->renderer->render('@home/index',
        ['siteName' => 'RentCar']);
    }

    public function list(ServerRequest $request): string {

        $autos = $this->manager->getRepository(Vehicule::class)->findAll();

        return $this->renderer->render('@home/list', [
            'autos' => $autos
        ]);
    }
}