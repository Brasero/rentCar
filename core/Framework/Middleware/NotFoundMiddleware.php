<?php
namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request): Response
    {
        return new Response(404, [], "Page introuvable");
    }
}