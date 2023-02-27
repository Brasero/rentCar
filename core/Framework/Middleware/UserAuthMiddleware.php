<?php
namespace Core\Framework\Middleware;

use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Router\Router;
use Core\Toaster\Toaster;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Verifie si la route est protégé grâce au début de l'url,
 * si oui s'assure que l'utilisateur a le droit d'y accéder
 */
class UserAuthMiddleware extends AbstractMiddleware
{
    use RedirectTrait;

    private ContainerInterface $container;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $container->get(Router::class);
    }

    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        if (str_starts_with($uri, '/user')) {
            $auth = $this->container->get(UserAuth::class);
            if (!$auth->isLogged() or !$auth->isUser()) {
                $toaster = $this->container->get(Toaster::class);
                $toaster->makeToast("Veuillez vous connecté pour continuer", Toaster::ERROR);
                return $this->redirect('user.login');
            }
        }
        return parent::process($request);
    }
}