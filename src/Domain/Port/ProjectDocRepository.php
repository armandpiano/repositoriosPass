<?php

declare(strict_types=1);

namespace App\Domain\Port;

use App\Domain\Entity\ProjectDoc;

interface ProjectDocRepository
{
    public function findByProjectIdAndType(int $projectId, string $docType): ?ProjectDoc;

    public function upsert(int $projectId, string $docType, string $htmlContent, \DateTimeImmutable $fetchedAt, string $hash): void;
}
