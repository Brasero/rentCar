<?php
namespace Core\Framework\Validator;

use Doctrine\ORM\EntityRepository;

class Validator
{
    private array $data;

    private array $errors;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string ...$keys): self
    {
        foreach($keys as $key) {
            if (!array_key_exists($key, $this->data) || $this->data[$key] === '' || $this->data[$key] === null){
                $this->addError($key, 'required');
            }
        }

        return $this;
    }

    public function email(string $key): self
    {
        if(!filter_var($this->data[$key], FILTER_VALIDATE_EMAIL))
        {
            $this->addError($key, 'email');
        }

        return $this;
    }

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

    public function isUnique(string $key, EntityRepository $repo, string $field = 'nom'): self
    {
        $all = $repo->findAll();
        $method = 'get' . ucfirst($field);
        foreach ($all as $item) {
            if (strcasecmp($item->$method(), $this->data[$key]) === 0)
            {
                $this->addError($key, 'unique');
                break;
            }
        }

        return $this;
    }

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