<?php
namespace Core\db;
use PDO;
use PDOException;

class Database
{

    private string $host;
    private string $user;
    private string $dbname;
    private string $mdp;
    private string $char;
    public PDO $connection;
    public static Database $instance;

    private function __construct(string $host, string $dbname, string $user, string $mdp, string $char) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->mdp = $mdp;
        $this->char = $char;
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset={$this->char}", $this->user, $this->mdp);
        } catch(PDOException $e) {
            echo "[ERREUR] => {$e->getMessage()}";
            die;
        }
    }


    public static function getInstance(string $host, string $dbname, string $user, string $mdp, string $char = 'utf8'){
        if(!isset(self::$instance) or empty(self::$instance)) {
            self::$instance = new Database($host, $dbname, $user, $mdp, $char);
        }
        return self::$instance;
    }
}