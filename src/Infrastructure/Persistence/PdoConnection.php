<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Config\Env;

class PdoConnection
{
    private $env;

    public function __construct(Env $env)
    {
        $this->env = $env;
    }

    public function create(): \PDO
    {
        $host = $this->env->get('DB_HOST', '127.0.0.1');
        $port = $this->env->get('DB_PORT', '3306');
        $db = $this->env->get('DB_NAME', 'hexagonal_dashboard');
        $user = $this->env->get('DB_USER', 'root');
        $pass = $this->env->get('DB_PASS', '');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
        $pdo = new \PDO($dsn, $user, $pass);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return $pdo;
    }
}
