<?php
namespace App\Car;

use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use Model\Entity\Vehicule;
use Psr\Http\Message\ServerRequestInterface;

class CarModule extends AbstractModule
{
    private Router $router;
    private RendererInterface $renderer;

    private $repository;

    private EntityManager $manager;

    public const DEFINITIONS = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->repository = $manager->getRepository(Vehicule::class);
        $this->manager = $manager;

        $this->renderer->addPath('car',__DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/addCar', [$this, 'addCar'], 'car.add');
        $this->router->get('/listCar', [$this, 'listCar'], 'car.list');
        $this->router->get('/show', [$this, 'show'], 'car.show');
        $this->router->get('/update/{id:[\d]+}', [$this, 'update'], 'car.update');
        $this->router->post('/addCar', [$this, 'addCar']);
    }

    public function addCar(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $new = new Vehicule();
            $new->setModel($data['modele'])
                ->setMarque($data['marque'])
                ->setCouleur($data['couleur']);

            $this->manager->persist($new);
            $this->manager->flush();

            return (new Response)
                ->withHeader('Location', '/listCar');
        }

        return $this->renderer->render('@car/addCar');
    }

    public function listCar(ServerRequestInterface $request): string {
        $voitures = $this->repository->findAll();

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

    public function update(ServerRequestInterface $request): string {

        return "";
    }
}