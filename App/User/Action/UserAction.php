<?php
namespace App\User\Action;


use Core\Framework\Auth\UserAuth;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Router\Router;
use Core\Framework\Validator\Validator;
use Core\Session\SessionInterface;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Model\Entity\User;
use Psr\Container\ContainerInterface;

class UserAction
{
    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;
    private Toaster $toaster;
    private EntityRepository $repository;
    private SessionInterface $session;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->toaster = $container->get(Toaster::class);
        $this->router = $container->get(Router::class);
        $this->repository = $container->get(EntityManager::class)->getRepository(User::class);
        $this->session = $container->get(SessionInterface::class);
        $user = $this->session->get('auth');
        if ($user) {
            $this->renderer->addGlobal('user', $user);
        }
    }

    public function logView(ServerRequest $request)
    {
        return $this->renderer->render('@user/forms');
    }

    public function signin(ServerRequest $request)
    {
        $auth = $this->container->get(UserAuth::class);
        $data = $request->getParsedBody();
        $validator = new Validator($data);
        $errors = $validator
            ->required('nom', 'prenom', 'mail', 'mdp', 'mdp_confirm')
            ->email('mail')
            ->strSize('mdp', 12, 50)
            ->confirm('mdp')
            ->isUnique('mail', $this->repository, 'mail')
            ->getErrors();

        if($errors)
        {
            foreach($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }
        $result = $auth->signIn($data);

        if($result !== true) {
            return $result;
        }
        $this->toaster->makeToast("Inscription reussie, vous pouvez vous connécter", Toaster::SUCCESS);
        return $this->redirect('user.login');
    }

    public function login(ServerRequest $request)
    {
        $data = $request->getParsedBody();

        $validator = new Validator($data);
        $errors = $validator
            ->required('mail', 'mdp')
            ->email('mail')
            ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }

        $auth = $this->container->get(UserAuth::class);
        $res = $auth->login($data['mail'], $data['mdp']);
        if ($res) {
            $this->toaster->makeToast('Connexion reussie', Toaster::SUCCESS);
            return $this->redirect('user.home');
        }
        $this->toaster->makeToast("Connexion échoué, merci de vérifier email et mot de passe", Toaster::ERROR);
        return $this->redirect('user.login');
    }

    public function home(ServerRequest $request)
    {
        $user = $this->session->get('auth');
        return $this->renderer->render('@user/home',[
            'user' => $user
        ]);
    }
}