<?php
namespace App\Car\Action;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Router\Router;
use Core\Framework\Validator\Validator;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\UploadedFile;
use Model\Entity\Marque;
use Model\Entity\Vehicule;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;

class CarAction
{
    use RedirectTrait;

    private RendererInterface $renderer;
    private EntityManager $manager;
    private Toaster $toaster;
    private $marqueRepository;
    private $repository;

    private ContainerInterface $container;

    private Router $router;

    public function __construct(
        RendererInterface $renderer,
        EntityManager $manager,
        Toaster $toaster,
        ContainerInterface $container
    )
    {
        $this->renderer = $renderer;
        $this->manager = $manager;
        $this->toaster = $toaster;
        $this->router = $container->get(Router::class);
        //Sers a manipuler les marques en base de données
        $this->marqueRepository = $manager->getRepository(Marque::class);
        //Sers a manipuler les vehicules en base de données
        $this->repository = $manager->getRepository(Vehicule::class);
        $this->container = $container;
    }

    /**
     * Methode ajoutant un vehicule en bdd
     * @param ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function addCar(ServerRequestInterface $request)
    {
        //Récupére la methode utilisé pour la requête (POST ou GET)
        $method = $request->getMethod();

        //Si le formulaire à été soumis
        if ($method === 'POST') {
            //On récupère le contenu de $_POST (les valeur saisie dans le formulaire)
            $data = $request->getParsedBody();
            //On récupère le contenu de $_FILES à l'index "image" (Les fichiers chargé dans le formulaire, avec un input de type 'file')
            $file = $request->getUploadedFiles()["image"];

            //On instancie le Validator en lui passant le tableau de données à valider
            $validator = new Validator($data);
            //On fixe les régles à respecter sur chaque input du formulaire, si il y en a, et on récupère les erreur ou null
            $errors = $validator
                ->required('modele', 'couleur', 'marque')
                ->getErrors();
            //Si il y a des erreur, on crée un Toast par erreur et on redirige l'utilisateur afin d'afficher les message
            if($errors) {
                //Boucle sur le tableau d'erreurs
                foreach($errors as $error) {
                    //Création du Toast
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                //Redirection
                return $this->redirect('car.add');
            }
            //On vérifie que l'image soit conforme (voir commentaire de la methode)
            $error = $this->fileGuards($file);
            //si on a des erreur on retourne le Toast (Le Toast a été générer par 'fileGuard')
            if ($error !== true) {
                return $error;
            }
            //Si tout va bien avec le fichier, on récupère le nom
            $fileName = $file->getClientFileName();
            //On assemble le nom du fichier avec le chemin du dossier ou il sera enregistré
            $imgPath = $this->container->get('img.basePath') . $fileName;
            //On tente de le déplacer au chemin voulu
            $file->moveTo($imgPath);
            //Si le déplacement n'est pas possible on créer un Toast et on redirige
            if (!$file->isMoved()) {
                $this->toaster->makeToast("Une erreur s'est produite durant l'enregistrement de votre image, merci de réessayer.", Toaster::ERROR);
                return $this->redirect('car.add');
            }
            //Si tout s'est bien passée on créer un nouveau véhicule
            $new = new Vehicule();
            //On récupère l'objet qui repésente la marque choisie
            $marque = $this->marqueRepository->find($data['marque']);
            //Si on a bien reussi à récuperer une marque, on complète les info du vehicule puis on l'enregistre
            if ($marque) {
                //Complétion des infos du véhicule
                $new->setModel($data['modele'])
                    ->setMarque($marque)
                    ->setCouleur($data['couleur'])
                    ->setImgPath($fileName);
                //Préparation à l'enregistrement en base de données
                $this->manager->persist($new);
                //Enregistrement en base de données
                $this->manager->flush();
                //Création d'un toast de succées
                $this->toaster->makeToast('Véhicule ajoutée avec success', Toaster::SUCCESS);
            }
            //Dans tout les cas on fini par redirigé
            return $this->redirect('car.list');
        }

        //On récupére les marques
        $marques = $this->marqueRepository->findAll();

        //On rend la vue en passant le tableau de marque en paramètre
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
        //On récupère les véhicules
        $voitures = $this->repository->findAll();

        //On rend la vue en passant le tableau de véhicules en paramètre
        return $this->renderer->render('@car/list', [
            "voitures" => $voitures
        ]);
    }

    /**
     * retourne le détails d'un vehicule en fonction de son id
     * @param ServerRequestInterface $request
     * @return string|Response
     */
    public function show(ServerRequestInterface $request) {

        //On récupère l'id passez en paramètre de requête
        $id = $request->getAttribute('id');

        //On récupère le véhicule qui correspond à l'id
        $voiture = $this->repository->find($id);

        if (!$voiture) {
            return new Response(404, [], 'Aucun vehicule ne correspond');
        }

        //On rend la vue en passant en paramètre le vehicule
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
        //On récupère l'id passez en paramètre de requête
        $id = $request->getAttribute('id');

        //On récupère le véhicule qui correspond à l'id
        $voiture = $this->repository->find($id);

        //On récupère la method utilisée par la requête
        $method = $request->getMethod();

        //Si le formulaire à été soumis
        if ($method === 'POST') {
            //On récupère les données saisie dans le formulaire ($_POST)
            $data = $request->getParsedBody();
            //On récupère les fichier chargé si il y en a, sinon un tableau vide
            $files = $request->getUploadedFiles();
            //On vérifie si un fichier à été chargé et qu'il n'y a pas eu d'erreur de chargement
            if (sizeof($files) > 0 && $files['image']->getError() !== 4) {
                //On récupère le nom de l'ancienne image du véhicule
                $oldImg = $voiture->getImgPath();
                //On récupère toutes les informations de la nouvelle image
                $newImg = $files['image'];
                //On récupère le nom de la nouvelle image
                $imgName = $newImg->getClientFileName();
                //On joint le nom de l'image au chemin du dossier ou l'in souhaite l'enregistrer
                $imgPath = $this->container->get('img.basePath') . $imgName;
                //On vérifie la nouvelle image
                $error = $this->fileGuards($newImg);
                //Si il y a une erreur avec le fichier on retourne l'erreur
                if ($error) {
                    return $error;
                }
                //On tente de la déplacer
                $newImg->moveTo($imgPath);
                //Si l'image à bien été déplacé
                if ($newImg->isMoved()) {
                    //On lie la nouvelle image avec le vehicule
                    $voiture->setImgPath($imgName);
                    //On supprime l'ancienne du server avec la fonction unlink
                    $oldPath = $this->container->get('img.basePath') . $oldImg;
                    unlink($oldPath);
                }
            }
            //On récupère la marque choisie
            $marque = $this->marqueRepository->find($data['marque']);
            //On modifie les données du véhicule ainsi que sa marque
            $voiture->setModel($data['modele'])
                ->setMarque($marque)
                ->setCouleur($data['couleur']);

            //On enregistre les modification
            $this->manager->flush();
            //On créer un Toast de success pour l'utilisateur
            $this->toaster->makeToast('Véhicule ajoutée avec success', Toaster::SUCCESS);
            //On redirige sur la liste des véhicule
            return $this->redirect('car.list');
        }
        //Si le formulaire n'a pas été soumis

        //On récupère les marques en base de données pour les utilisé dans le menu select de la vue
        $marques = $this->marqueRepository->findAll();

        //On retourne la vue avec le véhicule que l'on souhaite modifier et la liste des marques
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

        //On récupère l'id passez en paramètre de requête
        $id = $request->getAttribute('id');

        //On récupère le véhicule qui correspond à l'id
        $voiture = $this->repository->find($id);

        //On prépare l'objet à etre supprimer de la base de données
        $this->manager->remove($voiture);
        //On execute la suppression
        $this->manager->flush();

        //On récupère le nom de l'ancienne image du véhicule
        $oldImg = $voiture->getImgPath();
        //On supprime l'ancienne du server avec la fonction unlink
        $oldPath = $this->container->get('img.basePath') . $oldImg;
        unlink($oldPath);

        //On créer un Toast success pour l'utilisateur
        $this->toaster->makeToast('Véhicule supprimé', Toaster::SUCCESS);

        //On redirige sur la liste des véhicule
        return $this->redirect('car.list');
    }


    /**
     * Check si une image est conforme au restrictions de server
     * @param UploadedFile $file
     * @return MessageInterface|true
     */
    private function fileGuards(UploadedFile $file)
    {
        //Handle Server error
        //S'assure qu'il n'y a pas eu d'erreur au chargement de l'image
        if ($file->getError() === 4) {
            $this->toaster->makeToast("Une erreur est survenu lors du chargement du fichier.", Toaster::SUCCESS);
            return $this->redirect('car.add');
        }

        //list permet de décomposé le contenu d'un tableau afin d'en extraire les valeur et de les stockées dans des variables
        //On récupère le type et le format du fichier
        list($type, $format) = explode('/', $file->getClientMediaType());//getClientMediaType renvoi le type MIME d'un fichier
        // exemple de type MIME : image/jpg

        //Handle format error
        //On vérifie que le format et le type de fichier correspondent aux formats et type autorisé, sinon on renvoie une erreur
        if (!in_array($type, ['image']) or !in_array($format, ['jpg', 'jpeg', 'png']))
        {
            $this->toaster->makeToast(
                "ERREUR : Le format du fichier n'est pas valide, merci de charger un .png, .jpeg ou .jpg",
                Toaster::ERROR
            );
            return $this->redirect('car.add');
        }

        //Handle excessive size
        //Vérifie que la taille du fichier en octets ne dépasse pas les 2Mo
        if ($file->getSize() > 2047674) {
            $this->toaster->makeToast("Merci de choisir un fichier n'excédant pas 2Mo", Toaster::ERROR);
            return $this->redirect('car.add');
        }

        return true;
    }
}
