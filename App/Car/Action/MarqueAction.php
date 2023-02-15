<?php
namespace App\Car\Action;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Validator\Validator;
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
            $validator = new Validator($data);
            $errors = $validator
                ->required("marque")
                ->getErrors();
            if($errors) {
                foreach($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('Location', '/addMarque');
            }
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

    public function update(ServerRequestInterface $request)
    {
        $method = $request->getMethod();
        $id = $request->getAttribute('id');
        $marque = $this->marqueRepository->find($id);

        if ($method === 'POST')
        {
            $data = $request->getParsedBody();
            $validator = new Validator($data);
            $errors = $validator->required('marque')
                ->getErrors();
            if ($errors) {
                foreach($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('Location', '/updateMarque/'.$id);
            }
            $marque->setName($data['marque']);
            $this->manager->flush();
            $this->toaster->makeToast('Marque modifiée', Toaster::SUCCESS);
            return (new Response())
                ->withHeader('Location', '/marqueList');
        }

        return $this->renderer->render('@car/updateMarque', [
            'marque' => $marque
        ]);
    }

    public function delete(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $marque = $this->marqueRepository->find($id);

        $this->manager->remove($marque);
        $this->manager->flush();
        $this->toaster->makeToast('Marque supprimée.', Toaster::SUCCESS);
        return (new Response())
            ->withHeader('Location', '/marqueList');
    }
}