<?php
namespace Core\Framework\Validator;

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