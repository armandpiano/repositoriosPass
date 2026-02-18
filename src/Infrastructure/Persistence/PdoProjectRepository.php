<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Project;
use App\Domain\Port\ProjectRepository;

class PdoProjectRepository implements ProjectRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, project_url, docx_url, created_at FROM projects ORDER BY id ASC');
        $projects = [];
        foreach ($stmt->fetchAll() as $row) {
            $projects[] = new Project(
                (int) $row['id'],
                (string) $row['name'],
                (string) $row['project_url'],
                (string) $row['docx_url'],
                new \DateTimeImmutable((string) $row['created_at'])
            );
        }

        return $projects;
    }

    public function findById(int $id): ?Project
    {
        $stmt = $this->pdo->prepare('SELECT id, name, project_url, docx_url, created_at FROM projects WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        return new Project(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['project_url'],
            (string) $row['docx_url'],
            new \DateTimeImmutable((string) $row['created_at'])
        );
    }
}
