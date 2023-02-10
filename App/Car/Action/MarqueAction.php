<?php
namespace App\Car\Action;

use Core\Framework\Renderer\RendererInterface;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use Model\Entity\Marque;
use Psr\Http\Message\ServerRequestInterface;

class MarqueAction
{

    private RendererInterface $renderer;
    private EntityManager $manager;
    private Toaster $toaster;

    private $marqueRepository;

    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster)
    {

        $this->renderer = $renderer;
        $this->manager = $manager;
        $this->toaster = $toaster;
        $this->marqueRepository = $manager->getRepository(Marque::class);
    }

    public function addMarque(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $marques = $this->marqueRepository->findAll();

            foreach ($marques as $marque) {
                if ($marque->getName() === $data['marque']) {
                    $this->toaster->makeToast('Cette marque existe déjà', Toaster::ERROR);
                    return $this->renderer->render('@car/addMarque');
                }
            }

            $new = new Marque();
            $new->setName($data['marque']);
            $this->manager->persist($new);
            $this->manager->flush();
            $this->toaster->makeToast('Marque créée avec success', Toaster::SUCCESS);

            return (new Response())
                ->withHeader('Location', '/listCar');
        }

        return $this->renderer->render('@car/addMarque');
    }

    public function marqueList(ServerRequestInterface $request) {
        $marques = $this->marqueRepository->findAll();

        return $this->renderer->render('@car/listMarque', [
            'marques' => $marques
        ]);
    }
}