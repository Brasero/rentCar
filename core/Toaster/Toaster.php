<?php
namespace Core\Toaster;

use Core\Session\SessionInterface;

class Toaster
{
    private const SESSION_KEY = 'toast';

    const ERROR = 0;
    const WARNING = 1;
    const SUCCESS = 2;

    private Toast $toast;
    private SessionInterface $session;
    public function __construct(SessionInterface $session) {
        $this->session = $session;
        $this->toast = new Toast();
    }

    public function makeToast(string $message, int $etat): void
    {
        switch($etat) {
            case 0:
                $this->session->set(self::SESSION_KEY, $this->toast->error($message));
                break;

            case 1:
                $this->session->set(self::SESSION_KEY, $this->toast->warning($message));
                break;

            case 2:
                $this->session->set(self::SESSION_KEY, $this->toast->success($message));
                break;
        }
    }

    public function renderToast(): ?string
    {
        $toast = $this->session->get(self::SESSION_KEY);
        $this->session->delete(self::SESSION_KEY);
        return $toast;
    }

    public function hasToast(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }

}