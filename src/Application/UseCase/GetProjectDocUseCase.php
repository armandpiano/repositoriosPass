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

    public function execute(int $projectId): array
    {
        $project = $this->projectRepository->findById($projectId);
        if ($project === null) {
            return ['success' => false, 'html' => '', 'message' => 'Proyecto no encontrado.'];
        }

        $download = $this->docxDownloader->download($project->getDocxUrl());
        if (!$download['success']) {
            $html = '';
            if (($download['reason'] ?? '') === 'not_found') {
                $html = $this->buildNotFoundHtml((string) $download['message']);
            }

            return ['success' => false, 'html' => $html, 'message' => (string) $download['message']];
        }

        $hash = hash('sha256', (string) $download['content']);
        $cached = $this->projectDocRepository->findByProjectId($projectId);
        if ($cached !== null && $this->isCacheValid($cached->getFetchedAt()) && hash_equals($cached->getHash(), $hash)) {
            return ['success' => true, 'html' => $cached->getHtmlContent(), 'message' => 'OK (cache).'];
        }

        try {
            $html = $this->docxToHtmlConverter->convertToHtml((string) $download['content']);
        } catch (\Throwable $exception) {
            return ['success' => false, 'html' => '', 'message' => 'No fue posible convertir el documento en este momento.'];
        }

        $sanitized = $this->htmlSanitizer->sanitize($html);
        $now = new \DateTimeImmutable('now');
        $this->projectDocRepository->upsert($projectId, $sanitized, $now, $hash);

        return ['success' => true, 'html' => $sanitized, 'message' => 'OK (actualizado).'];
    }

    private function isCacheValid(\DateTimeImmutable $fetchedAt): bool
    {
        $expiry = $fetchedAt->modify('+' . $this->cacheTtlHours . ' hours');

        return $expiry >= new \DateTimeImmutable('now');
    }

    private function buildNotFoundHtml(string $message): string
    {
        $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        return '<section class="p-3"><div class="alert alert-warning mb-0"><h6 class="fw-bold mb-2"><i class="bi bi-file-earmark-x-fill me-2"></i>Archivo no encontrado</h6><p class="mb-0">' . $safeMessage . '</p></div></section>';
    }
}
