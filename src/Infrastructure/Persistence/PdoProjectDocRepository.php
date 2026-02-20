<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\ProjectDoc;
use App\Domain\Port\ProjectDocRepository;

class PdoProjectDocRepository implements ProjectDocRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByProjectIdAndType(int $projectId, string $docType): ?ProjectDoc
    {
        $stmt = $this->pdo->prepare('SELECT id, project_id, doc_type, html_content, fetched_at, hash FROM project_docs WHERE project_id = :project_id AND doc_type = :doc_type LIMIT 1');
        $stmt->execute([
            'project_id' => $projectId,
            'doc_type' => $docType,
        ]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        return new ProjectDoc(
            (int) $row['id'],
            (int) $row['project_id'],
            (string) $row['doc_type'],
            (string) $row['html_content'],
            new \DateTimeImmutable((string) $row['fetched_at']),
            (string) $row['hash']
        );
    }

    public function upsert(int $projectId, string $docType, string $htmlContent, \DateTimeImmutable $fetchedAt, string $hash): void
    {
        $sql = 'INSERT INTO project_docs (project_id, doc_type, html_content, fetched_at, hash)
                VALUES (:project_id, :doc_type, :html_content, :fetched_at, :hash)
                ON DUPLICATE KEY UPDATE html_content = VALUES(html_content), fetched_at = VALUES(fetched_at), hash = VALUES(hash)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'project_id' => $projectId,
            'doc_type' => $docType,
            'html_content' => $htmlContent,
            'fetched_at' => $fetchedAt->format('Y-m-d H:i:s'),
            'hash' => $hash,
        ]);
    }
}
