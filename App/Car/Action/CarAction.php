<?php
namespace App\Car\Action;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Validator\Validator;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\UploadedFile;
use Model\Entity\Marque;
use Model\Entity\Vehicule;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;

class CarAction
{
    private RendererInterface $renderer;
    private EntityManager $manager;
    private Toaster $toaster;
    private $marqueRepository;
    private $repository;
    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster)
    {
        $this->renderer = $renderer;
        $this->manager = $manager;
        $this->toaster = $toaster;
        $this->marqueRepository = $manager->getRepository(Marque::class);
        $this->repository = $manager->getRepository(Vehicule::class);
    }

    /**
     * Methode ajoutant un vehicule en bdd
     * @param ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function addCar(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $file = $request->getUploadedFiles()["image"];

            $validator = new Validator($data);
            $errors = $validator
                ->required('modele', 'couleur', 'marque')
                ->getErrors();
            if($errors) {
                foreach($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('Location', '/admin/addCar');
            }
            $this->fileGuards($file);
            $fileName = $file->getClientFileName();
            $imgPath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fileName;
            $file->moveTo($imgPath);
            if (!$file->isMoved()) {
                $this->toaster->makeToast("Une erreur s'est produite durant l'enregistrement de votre image, merci de réessayer.", Toaster::ERROR);
                return (new Response())
                    ->withHeader('Location', '/admin/addCar');
            }
            $new = new Vehicule();
            $marque = $this->marqueRepository->find($data['marque']);
            if ($marque) {
                $new->setModel($data['modele'])
                    ->setMarque($marque)
                    ->setCouleur($data['couleur'])
                    ->setImgPath($imgPath);

                $this->manager->persist($new);
                $this->manager->flush();
                $this->toaster->makeToast('Véhicule ajoutée avec success', Toaster::SUCCESS);
            }

            return (new Response)
                ->withHeader('Location', '/admin/listCar');
        }

        $marques = $this->marqueRepository->findAll();

        return $this->renderer->render('@car/addCar', [
            'marques' => $marques
        ]);
    }

    /**
     * Retourne la liste des vehicule en bdd
     * @param ServerRequestInterface $request
     * @return string
     */
    public function listCar(ServerRequestInterface $request): string {
        $voitures = $this->repository->findAll();

        return $this->renderer->render('@car/list', [
            "voitures" => $voitures
        ]);
    }

    /**
     * retourne le détails d'un vehicule en fonction de son id
     * @param ServerRequestInterface $request
     * @return string
     */
    public function show(ServerRequestInterface $request): string {

        $id = $request->getAttribute('id');

        $voiture = $this->repository->find($id);

        return $this->renderer->render('@car/show', [
            "voiture" => $voiture
        ]);
    }


    /**
     * Modifie un véhicule en bdd
     * @param ServerRequestInterface $request
     * @return MessageInterface|string
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(ServerRequestInterface $request) {
        $id = $request->getAttribute('id');
        $voiture = $this->repository->find($id);

        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $marque = $this->marqueRepository->find($data['marque']);
            $voiture->setModel($data['modele'])
                ->setMarque($marque)
                ->setCouleur($data['couleur']);

            $this->manager->flush();
            $this->toaster->makeToast('Véhicule ajoutée avec success', Toaster::SUCCESS);
            return (new Response)
                ->withHeader('Location', '/admin/listCar');
        }

        $marques = $this->marqueRepository->findAll();

        return $this->renderer->render('@car/update', [
            'voiture' => $voiture,
            'marques' => $marques
        ]);
    }

    /**
     * Supprime un vehicule de la bdd
     * @param ServerRequestInterface $request
     * @return MessageInterface
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(ServerRequestInterface $request) {
        $id = $request->getAttribute('id');
        $voiture = $this->repository->find($id);

        $this->manager->remove($voiture);
        $this->manager->flush();

        $this->toaster->makeToast('Véhicule supprimé', Toaster::SUCCESS);

        return (new Response())
            ->withHeader('Location', '/admin/listCar');
    }

    private function fileGuards(UploadedFile $file)
    {
        //Handle Server error
        if ($file->getError() === 4) {
            $this->toaster->makeToast("Une erreur est survenu lors du chargement du fichier.", Toaster::SUCCESS);
            return (new Response())
                ->withHeader('Location', '/admin/addCar');
        }

        list($type, $format) = explode('/', $file->getClientMediaType());

        //Handle format error
        if (!in_array($type, ['image']) or !in_array($format, ['jpg', 'jpeg', 'png']))
        {
            $this->toaster->makeToast(
                "ERREUR : Le format du fichier n'est pas valide, merci de charger un .png, .jpeg ou .jpg",
                Toaster::ERROR
            );
            return (new Response())
                ->withHeader('Location', '/admin/addCar');
        }

        //Handle excessive size
        if ($file->getSize() > 2047674) {
            $this->toaster->makeToast("Merci de choisir un fichier n'excédant pas 2Mo", Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/addCar');
        }

        return true;
    }
}