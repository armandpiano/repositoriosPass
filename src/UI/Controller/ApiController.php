<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\UseCase\GetProjectDocUseCase;
use App\Application\UseCase\ListProjectsUseCase;

class ApiController
{
    private ListProjectsUseCase $listProjectsUseCase;
    private GetProjectDocUseCase $getProjectDocUseCase;

    public function __construct(ListProjectsUseCase $listProjectsUseCase, GetProjectDocUseCase $getProjectDocUseCase)
    {
        $this->listProjectsUseCase = $listProjectsUseCase;
        $this->getProjectDocUseCase = $getProjectDocUseCase;
    }

    public function projects(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['data' => $this->listProjectsUseCase->execute()]);
    }

    public function projectDoc(int $projectId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $result = $this->getProjectDocUseCase->execute($projectId);
        echo json_encode($result);
    }
}
