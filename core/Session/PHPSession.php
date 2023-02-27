<?php
namespace Core\Session;

class PHPSession implements SessionInterface {


    public function __construct() {
        $this->startIfNot();
    }

    public function set(string $key, $value): void
    {
        $this->startIfNot();
        $_SESSION[$key] = $value;
    }

    public function setArray(string $key, $value): void
    {
        $this->startIfNot();
        $_SESSION[$key][] = $value;
    }

    public function get(string $key, $default = null)
    {
        $this->startIfNot();
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        $this->startIfNot();
        return isset($_SESSION[$key]);
    }

    public function delete(string $key): void
    {
        $this->startIfNot();
        unset($_SESSION[$key]);
    }


    private function startIfNot() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}