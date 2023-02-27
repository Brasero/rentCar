<?php
namespace Core\Framework\Validator;

use Doctrine\ORM\EntityRepository;

class Validator
{
    private array $data;

    private array $errors;

    /**
     * Enregistre le tableau de données à valider
     * @param array $data tableau de données (habituellement il s'agit du tableau récupérer par $request->getParsedBody())
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Liste les index attendu et obligatoire dans le tableau de données
     * @param string ...$keys liste de chaine de caractères,
     * "...$keys" permet de précisé que l'on s'attend a un nombre indéfinie de valeurs
     * @return $this
     */
    public function required(string ...$keys): self
    {
        foreach($keys as $key) {
            if (!array_key_exists($key, $this->data) || $this->data[$key] === '' || $this->data[$key] === null){
                $this->addError($key, 'required');
            }
        }

        return $this;
    }


    /**
     * S'assure que le champs est une adresse email valide
     * @param string $key
     * @return $this
     */
    public function email(string $key): self
    {
        //filter_var fonction native qui permet de verifier la conformité d'une valeur en fontion d'un filtre cf: php manual
        if(!filter_var($this->data[$key], FILTER_VALIDATE_EMAIL))
        {
            $this->addError($key, 'email');
        }

        return $this;
    }

    /**
     * S'assure que le nombre de caractère d'une chaine soit bien compris entre un minimum et un maximum
     * @param string $key
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function strSize(string $key, int $min, int $max): self
    {
        if (!array_key_exists($key, $this->data)) {
            return $this;
        }
        $length = mb_strlen($this->data[$key]);
        if ($length < $min) {
            $this->addError($key, 'strMin');
        }
        if ($length > $max) {
            $this->addError($key, 'strMax');
        }
        return $this;
    }

    /**
     * S'assure que le champ saisi possède la même valeur que son champ de confirmation
     * Si la valeur de $key est "mdp" le champ de confirmation doit absolument se nommée "mdp_confirm"
     * @param string $key
     * @return $this
     */
    public function confirm(string $key): self
    {
        $confirm = $key . '_confirm';
        if (!array_key_exists($key, $this->data)) {
            return $this;
        }
        if (!array_key_exists($confirm, $this->data)) {
            return $this;
        }
        if ($this->data[$key] !== $this->data[$confirm]) {
            $this->addError($key, 'confirm');
        }

        return $this;
    }

    /**
     * S'assure qu'une valeur soit unique en base de données
     * @param string $key Index du tableau
     * @param EntityRepository $repo repositories doctrine de l'élément a vérifier
     * @param string $field champ a verifier en base de données (par défaut vaut nom)
     * @return $this
     */
    public function isUnique(string $key, EntityRepository $repo, string $field = 'nom'): self
    {
        //Récupére toutes les entité du repositories
        $all = $repo->findAll();
        //Créer le nom de la methode utilisable pour récuperer la valeur
        // (exemple: si $field = 'model' alors $method = 'getModel')
        $method = 'get' . ucfirst($field);
        //On boucle sur tout les enregistrement de la base de données
        foreach ($all as $item) {
            //On vérifie si la valeur saisie par l'utilisateur correspond à une valeur existante en base de données
            //sans tenir compte des accents, si c'est le cas on soulève une erreur
            if (strcasecmp($item->$method(), $this->data[$key]) === 0)
            {
                $this->addError($key, 'unique');
                break;
            }
        }

        return $this;
    }

    /**
     * Renvoie le tableau d'erreur, doit être appelé seulement après les autres methodes
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors ?? null;
    }

    private function addError(string $key, string $rule): void
    {
        if (!isset($this->errors[$key])) {
            $this->errors[$key] = new ValidatorError($key, $rule);
        }
    }
}