<?php
namespace Core\Framework\Router;

class Route
{
    private string $name;
    private $callable;
    private array $params;

    /**
     * Enregistre les informations liée a la route
     * @param string $name Nom de la route (Exemple : user.login)
     * @param $callable /Fonction de controller a appeler lors du match de la route
     * @param array $params Tableau de paramètre de la route
     */
    public function __construct(string $name, $callable, array $params)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->params = $params;
    }


    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Retourne la fonction de controller liée a la route
     */
    public function getCallback()
    {
        return $this->callable;
    }


    public function getParams(): array
    {
        return $this->params;
    }


}