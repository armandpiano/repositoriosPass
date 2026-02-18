<?php

declare(strict_types=1);

namespace App\Domain\Port;

use App\Domain\Entity\ProjectDoc;

interface ProjectDocRepository
{
    public function findByProjectId(int $projectId): ?ProjectDoc;

    public function upsert(int $projectId, string $htmlContent, \DateTimeImmutable $fetchedAt, string $hash): void;
}
