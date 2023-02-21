<?php
namespace App\User\Action;


use Core\Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;

class UserAction
{

    private ContainerInterface $container;
    private RendererInterface $renderer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
    }

    public function logView(ServerRequest $request)
    {
        return $this->renderer->render('@user/forms');
    }
}