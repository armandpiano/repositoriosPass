<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Port\ProjectRepository;

class ListProjectsUseCase
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function execute(): array
    {
        $projects = $this->projectRepository->findAll();
        $rows = [];
        foreach ($projects as $project) {
            $rows[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'company' => $project->getCompany(),
                'project_url' => $project->getProjectUrl(),
            ];
        }

        return $rows;
    }
}
