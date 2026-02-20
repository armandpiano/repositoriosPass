<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Port\ProjectDocRepository;
use App\Domain\Port\ProjectRepository;
use App\Infrastructure\DocFetcher\DocxDownloader;
use App\Infrastructure\DocFetcher\DocxToHtmlConverter;
use App\Infrastructure\DocFetcher\HtmlSanitizer;

class GetProjectDocUseCase
{
    private $projectRepository;
    private $projectDocRepository;
    private $docxDownloader;
    private $docxToHtmlConverter;
    private $htmlSanitizer;
    private $cacheTtlHours;

    public function __construct(
        ProjectRepository $projectRepository,
        ProjectDocRepository $projectDocRepository,
        DocxDownloader $docxDownloader,
        DocxToHtmlConverter $docxToHtmlConverter,
        HtmlSanitizer $htmlSanitizer,
        int $cacheTtlHours
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectDocRepository = $projectDocRepository;
        $this->docxDownloader = $docxDownloader;
        $this->docxToHtmlConverter = $docxToHtmlConverter;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->cacheTtlHours = $cacheTtlHours;
    }

    public function execute(int $projectId, string $docType): array
    {
        if (!in_array($docType, ['doc', 'func'], true)) {
            return ['html' => $this->buildErrorHtml('Tipo de documento no soportado.'), 'title' => 'Documento', 'type' => $docType];
        }

        $project = $this->projectRepository->findById($projectId);
        if ($project === null) {
            return ['html' => $this->buildErrorHtml('Proyecto no encontrado.'), 'title' => 'Documento', 'type' => $docType];
        }

        $filename = $docType === 'doc' ? $project->getDocFilename() : $project->getFuncFilename();
        $download = $this->docxDownloader->downloadByType($filename, $docType);
        if (!$download['success']) {
            return [
                'html' => $this->buildErrorHtml((string) $download['message']),
                'title' => $project->getName(),
                'type' => $docType,
            ];
        }

        $hash = hash('sha256', (string) $download['content']);
        $cached = $this->projectDocRepository->findByProjectIdAndType($projectId, $docType);
        if ($cached !== null && $this->isCacheValid($cached->getFetchedAt()) && hash_equals($cached->getHash(), $hash)) {
            return ['html' => $cached->getHtmlContent(), 'title' => $project->getName(), 'type' => $docType];
        }

        try {
            $html = $this->docxToHtmlConverter->convertToHtml((string) $download['content']);
        } catch (\Throwable $exception) {
            return ['html' => $this->buildErrorHtml('No fue posible convertir el documento en este momento.'), 'title' => $project->getName(), 'type' => $docType];
        }

        $sanitized = $this->htmlSanitizer->sanitize($html);
        $this->projectDocRepository->upsert($projectId, $docType, $sanitized, new \DateTimeImmutable('now'), $hash);

        return ['html' => $sanitized, 'title' => $project->getName(), 'type' => $docType];
    }

    private function isCacheValid(\DateTimeImmutable $fetchedAt): bool
    {
        $expiry = $fetchedAt->modify('+' . $this->cacheTtlHours . ' hours');

        return $expiry >= new \DateTimeImmutable('now');
    }

    private function buildErrorHtml(string $message): string
    {
        $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        return '<section class="viewer-error"><i class="bi bi-file-earmark-x"></i><h6>Contenido no disponible</h6><p>' . $safeMessage . '</p></section>';
    }
}
