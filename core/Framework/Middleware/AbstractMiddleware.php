<?php
namespace Core\Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    protected MiddlewareInterface $next;

    public function linkWith(MiddlewareInterface $middleware): MiddlewareInterface
    {
        $this->next = $middleware;
        return $middleware;
    }

    public function process(ServerRequestInterface $request)
    {
        return $this->next->process($request);
    }
}