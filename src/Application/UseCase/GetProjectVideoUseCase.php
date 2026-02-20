<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Port\ProjectRepository;

class GetProjectVideoUseCase
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function execute(int $projectId, string $basePath): array
    {
        $project = $this->projectRepository->findById($projectId);
        if ($project === null) {
            return ['videoUrl' => '', 'title' => 'Proyecto', 'type' => 'video', 'message' => 'Proyecto no encontrado.'];
        }

        $filename = basename($project->getVideoFilename());
        if ($filename === '' || $filename !== $project->getVideoFilename() || strpos($filename, '..') !== false) {
            return ['videoUrl' => '', 'title' => $project->getName(), 'type' => 'video', 'message' => 'El archivo de video no es válido.'];
        }

        return [
            'videoUrl' => rtrim($basePath, '/') . '/media/video/' . rawurlencode($filename),
            'title' => $project->getName(),
            'type' => 'video',
            'message' => 'OK',
        ];
    }
}
