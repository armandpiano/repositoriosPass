<?php

declare(strict_types=1);

namespace App\UI\Controller;

class MediaController
{
    private $projectRoot;
    private $videoDir;

    public function __construct(string $projectRoot, string $videoDir)
    {
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
        $this->videoDir = trim($videoDir);
    }

    public function serveVideo(string $filename): void
    {
        $safeFilename = basename(trim($filename));
        if ($safeFilename === '' || $safeFilename !== trim($filename) || strpos($safeFilename, '..') !== false) {
            $this->sendNotFound();
            return;
        }

        $extension = strtolower(pathinfo($safeFilename, PATHINFO_EXTENSION));
        $mimeMap = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
        ];

        if (!array_key_exists($extension, $mimeMap)) {
            $this->sendNotFound();
            return;
        }

        $basePath = $this->projectRoot . DIRECTORY_SEPARATOR . trim($this->videoDir, '/\\');
        $baseRealPath = realpath($basePath);
        if ($baseRealPath === false || !is_dir($baseRealPath)) {
            $this->sendNotFound();
            return;
        }

        $candidatePath = $baseRealPath . DIRECTORY_SEPARATOR . $safeFilename;
        $realFilePath = realpath($candidatePath);
        $basePrefix = rtrim($baseRealPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if ($realFilePath === false || !is_file($realFilePath) || strpos($realFilePath, $basePrefix) !== 0) {
            $this->sendNotFound();
            return;
        }

        header('Content-Type: ' . $mimeMap[$extension]);
        header('Content-Length: ' . (string) filesize($realFilePath));
        header('Content-Disposition: inline; filename="' . rawurlencode($safeFilename) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Accept-Ranges: bytes');
        readfile($realFilePath);
    }

    private function sendNotFound(): void
    {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Archivo de video no disponible.';
    }
}
