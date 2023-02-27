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
                $this->session->setArray(self::SESSION_KEY, $this->toast->error($message));
                break;

            case 1:
                $this->session->setArray(self::SESSION_KEY, $this->toast->warning($message));
                break;

            case 2:
                $this->session->setArray(self::SESSION_KEY, $this->toast->success($message));
                break;
        }
    }

    /**
     * Retourne les Toasts si il y en a
     * @return array|null
     */
    public function renderToast(): ?array
    {
        //On récupère tout les Toast enregistrer en session et on les stocks dans une variable
        $toast = $this->session->get(self::SESSION_KEY);
        //On supprime les Toasts de la session mais on les conserve dans la variable $toast
        $this->session->delete(self::SESSION_KEY);
        //On retourne les Toasts contenu dans la variable $toast
        return $toast;
    }

    /**
     * Verifie si il y a des Toasts à afficher, retourne true si oui sinon false
     * @return bool
     */
    public function hasToast(): bool
    {
        if($this->session->has(self::SESSION_KEY) && sizeof($this->session->get(self::SESSION_KEY)) > 0){
            return true;
        }

        return false;
    }

}