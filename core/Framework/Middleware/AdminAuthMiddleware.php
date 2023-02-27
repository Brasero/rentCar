<?php
namespace Core\Framework\Middleware;

use Core\Framework\Auth\AdminAuth;
use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Verifie si la route est protégé grâce au début de l'url,
 * si oui s'assure que l'utilisateur a le droit d'y accéder
 */
class AdminAuthMiddleware extends AbstractMiddleware
{

    private ContainerInterface $container;
    private Toaster $toaster;

    public function __construct(ContainerInterface $container)
    {

        $this->container = $container;
        $this->toaster = $container->get(Toaster::class);
    }

    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        //On vérifie si l'url commence par '/admin' et n'est pas égale à '/admin/login'
        if (str_starts_with($uri, '/admin') && $uri !== '/admin/login')
        {
            //On récupère l'objet qui gére l'administrateur
            $auth = $this->container->get(AdminAuth::class);
            //On verifie si l'administrateur et connécté et qu'il s'agit bien d'un administrateur
            if (!$auth->isLogged()) {
                if(!$auth->isLogged()){
                    //Si personne n'est connécté on renvoi un message en consequence
                    $this->toaster->makeToast("Vous devez être connecté pour accéder à cette page", Toaster::ERROR);
                } elseif(!$auth->isAdmin()){
                    //Si quelqu'un est connécté mais n'est pas un administrateur on lui refuse l'accès
                    $this->toaster->makeToast("Vous ne possédez pas les droits d'accès", Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('Location', '/');
            }
        }

        return parent::process($request);
    }
}