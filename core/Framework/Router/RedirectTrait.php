<?php
namespace Core\Framework\Router;

use GuzzleHttp\Psr7\Response;

trait RedirectTrait
{
    public function redirect(string $name, array $params = [])
    {
        $path = $this->router-> generateUri($name, $params);
        return (new Response)
            ->withHeader('Location', $path);
    }
}