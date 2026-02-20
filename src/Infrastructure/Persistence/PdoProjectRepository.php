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
        $stmt = $this->pdo->query('SELECT id, name, company, project_url, doc_filename, func_filename, video_filename, created_at FROM projects ORDER BY id ASC');
        $projects = [];
        foreach ($stmt->fetchAll() as $row) {
            $projects[] = $this->mapProject($row);
        }

        return $projects;
    }

    public function findById(int $id): ?Project
    {
        $stmt = $this->pdo->prepare('SELECT id, name, company, project_url, doc_filename, func_filename, video_filename, created_at FROM projects WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        return $this->mapProject($row);
    }

    private function mapProject(array $row): Project
    {
        return new Project(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['company'],
            (string) $row['project_url'],
            (string) $row['doc_filename'],
            (string) $row['func_filename'],
            (string) $row['video_filename'],
            new \DateTimeImmutable((string) $row['created_at'])
        );
    }
}
