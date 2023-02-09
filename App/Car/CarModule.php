<?php
namespace App\Car;

use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use Model\Entity\Marque;
use Model\Entity\Vehicule;
use Psr\Http\Message\ServerRequestInterface;

class CarModule extends AbstractModule
{
    private Router $router;
    private RendererInterface $renderer;

    private $repository;

    private $marqueRepository;

    private EntityManager $manager;

    public const DEFINITIONS = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->repository = $manager->getRepository(Vehicule::class);
        $this->manager = $manager;
        $this->marqueRepository = $manager->getRepository(Marque::class);

        $this->renderer->addPath('car',__DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/addCar', [$this, 'addCar'], 'car.add');
        $this->router->get('/listCar', [$this, 'listCar'], 'car.list');
        $this->router->get('/show/{id:[\d]+}', [$this, 'show'], 'car.show');
        $this->router->get('/update/{id:[\d]+}', [$this, 'update'], 'car.update');
        $this->router->post('/update/{id:[\d]+}', [$this, 'update']);
        $this->router->post('/addCar', [$this, 'addCar']);
        $this->router->get('/delete/{id:[\d]+}', [$this, 'delete'], 'car.delete');
        $this->router->get('/addMarque', [$this, 'addMarque'], 'marque.add');
        $this->router->post('/addMarque', [$this, 'addMarque']);
    }

    public function addMarque(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $marques = $this->marqueRepository->findAll();

            foreach ($marques as $marque) {
                if ($marque->getName() === $data['marque']) {
                    return $this->renderer->render('@car/addMarque');
                }
            }

            $new = new Marque();
            $new->setName($data['marque']);
            $this->manager->persist($new);
            $this->manager->flush();

            return (new Response())
                ->withHeader('Location', '/listCar');
        }

        return $this->renderer->render('@car/addMarque');
    }

    public function addCar(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $new = new Vehicule();
            $marque = $this->marqueRepository->find($data['marque']);
            $new->setModel($data['modele'])
                ->setMarque($marque)
                ->setCouleur($data['couleur']);

            $this->manager->persist($new);
            $this->manager->flush();

            return (new Response)
                ->withHeader('Location', '/listCar');
        }

        $marques = $this->marqueRepository->findAll();

        return $this->renderer->render('@car/addCar', [
            'marques' => $marques
        ]);
    }

    public function listCar(ServerRequestInterface $request): string {
        $voitures = $this->repository->findAll();

        return $this->renderer->render('@car/list', [
            "voitures" => $voitures
        ]);
    }

    public function show(ServerRequestInterface $request): string {

        $id = $request->getAttribute('id');

        $voiture = $this->repository->find($id);

        return $this->renderer->render('@car/show', [
            "voiture" => $voiture
        ]);
    }

    public function update(ServerRequestInterface $request) {
        $id = $request->getAttribute('id');
        $voiture = $this->repository->find($id);

        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $voiture->setModel($data['modele'])
                ->setMarque($data['marque'])
                ->setCouleur($data['couleur']);

            $this->manager->flush();
            return (new Response)
                ->withHeader('Location', '/listCar');
        }


        return $this->renderer->render('@car/update', [
            'voiture' => $voiture
        ]);
    }

    public function delete(ServerRequestInterface $request) {
        $id = $request->getAttribute('id');
        $voiture = $this->repository->find($id);

        $this->manager->remove($voiture);
        $this->manager->flush();

        return (new Response())
            ->withHeader('Location', '/listCar');
    }


}