<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\User;
use App\Domain\Port\UserRepository;

class PdoUserRepository implements UserRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare('SELECT id, username, password_hash, created_at FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        return new User(
            (int) $row['id'],
            (string) $row['username'],
            (string) $row['password_hash'],
            new \DateTimeImmutable((string) $row['created_at'])
        );
    }
}
