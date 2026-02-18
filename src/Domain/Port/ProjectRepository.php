<?php

declare(strict_types=1);

namespace App\Domain\Port;

use App\Domain\Entity\Project;

interface ProjectRepository
{
    /**
     * @return Project[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Project;
}
