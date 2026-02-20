<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\UseCase\GetProjectDocUseCase;
use App\Application\UseCase\GetProjectVideoUseCase;
use App\Application\UseCase\ListProjectsUseCase;

class ApiController
{
    private $listProjectsUseCase;
    private $getProjectDocUseCase;
    private $getProjectVideoUseCase;
    private $basePath;

    public function __construct(
        ListProjectsUseCase $listProjectsUseCase,
        GetProjectDocUseCase $getProjectDocUseCase,
        GetProjectVideoUseCase $getProjectVideoUseCase,
        string $basePath
    ) {
        $this->listProjectsUseCase = $listProjectsUseCase;
        $this->getProjectDocUseCase = $getProjectDocUseCase;
        $this->getProjectVideoUseCase = $getProjectVideoUseCase;
        $this->basePath = rtrim($basePath, '/');
    }

    public function projects(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['data' => $this->listProjectsUseCase->execute()]);
    }

    public function projectDoc(int $projectId, string $docType): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->getProjectDocUseCase->execute($projectId, $docType));
    }

    public function projectVideo(int $projectId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->getProjectVideoUseCase->execute($projectId, $this->basePath));
    }
}
