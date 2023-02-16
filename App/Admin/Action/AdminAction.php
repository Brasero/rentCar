<?php
namespace App\Admin\Action;

use Core\Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;

class AdminAction
{
    private ContainerInterface $container;
    private RendererInterface $renderer;

    public function __construct(ContainerInterface $container){

        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);

    }

    public function home(ServerRequest $request)
    {
        return $this->renderer->render('@admin/home');
    }
}