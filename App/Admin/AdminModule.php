<?php

namespace App\Admin;

use App\Admin\Action\AuthAction;
use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;

class AdminModule extends AbstractModule
{

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR .'config.php';

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->router = $container->get(Router::class);
        $authAction = $container->get(AuthAction::class);

        $this->renderer->addPath('admin',  __DIR__.DIRECTORY_SEPARATOR.'views');

        $this->router->get('/admin/login', [$authAction, 'login'], 'admin.login');
        $this->router->post('/admin/login', [$authAction, 'login']);
    }
}