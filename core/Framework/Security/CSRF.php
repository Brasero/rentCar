<?php

namespace Core\Framework\Security;


use Core\Session\SessionInterface;
use Psr\Container\ContainerInterface;

class CSRF
{

    private string $sessionKey = '_csrf_token';
    private string $formKey = '_csrf';

    private SessionInterface $session;

    public function __construct(ContainerInterface $container)
    {
        $this->session = $container->get(SessionInterface::class);
    }

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->session->setArray($this->sessionKey, $token);
        $this->limitToken();
        return "<input type='hidden' name='{$this->formKey}' value='{$token}'>";
    }

    public function checkToken(string $token = null): bool
    {
        if(!is_null($token)) {
            $tokens = $this->session->get($this->sessionKey, []);
            $key = array_search($token, $tokens, true);
            if($key !== false) {
                $this->consumeToken($token);
                return true;
            }
            return false;
        }
        return false;
    }

    public function getFormKey(): string
    {
        return $this->formKey;
    }

    private function limitToken(): void
    {
        $tokens = $this->session->get($this->sessionKey, []);
        if(count($tokens) > 10) {
            array_shift($tokens);
            $this->session->set($this->sessionKey, $tokens);
        }
    }

    private function consumeToken(string $token): void
    {
        $tokens = array_reduce($this->session->get($this->sessionKey, []), function($tok) use ($token){
            if ($tok !== $token) {
                return $tok;
            }
        }, []);
        $this->session->set($this->sessionKey, $tokens);
    }

}