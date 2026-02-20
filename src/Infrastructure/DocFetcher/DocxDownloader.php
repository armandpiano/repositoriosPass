<?php

declare(strict_types=1);

namespace App\Infrastructure\DocFetcher;

class DocxDownloader
{
    private $projectRoot;
    private $documentationDir;

    public function __construct(string $projectRoot, string $documentationDir)
    {
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
        $this->documentationDir = trim($documentationDir);
    }

    public function download(string $source): array
    {
        if (trim($source) === '') {
            return ['success' => false, 'content' => '', 'message' => 'La referencia del documento no está configurada.', 'reason' => 'empty'];
        }

        if ($this->isHttpUrl($source)) {
            return $this->downloadRemote($source);
        }

        return $this->downloadLocal($source);
    }

    private function isHttpUrl(string $source): bool
    {
        return preg_match('#^https?://#i', trim($source)) === 1;
    }

    private function downloadRemote(string $url): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return ['success' => false, 'content' => '', 'message' => 'No fue posible iniciar la descarga del documento.', 'reason' => 'curl_init'];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'HexagonalDashboard/1.0',
        ]);

        $content = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if (!is_string($content) || $content === '') {
            return ['success' => false, 'content' => '', 'message' => 'No se pudo descargar el documento. ' . ($curlError !== '' ? 'Detalle: ' . $curlError : ''), 'reason' => 'download'];
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return ['success' => false, 'content' => '', 'message' => 'No se pudo descargar el documento (HTTP ' . $httpCode . ').', 'reason' => 'http'];
        }

        return ['success' => true, 'content' => $content, 'message' => 'OK', 'reason' => 'remote'];
    }

    private function downloadLocal(string $docFileName): array
    {
        $safeFileName = basename(trim($docFileName));
        if ($safeFileName === '' || $safeFileName !== trim($docFileName) || strpos($safeFileName, '..') !== false) {
            return ['success' => false, 'content' => '', 'message' => 'El nombre del archivo de documentación no es válido.', 'reason' => 'invalid_filename'];
        }

        $basePath = $this->projectRoot . DIRECTORY_SEPARATOR . trim($this->documentationDir, '/\\');
        $baseRealPath = realpath($basePath);
        if ($baseRealPath === false || !is_dir($baseRealPath)) {
            return ['success' => false, 'content' => '', 'message' => 'No se encontró la carpeta de documentación configurada.', 'reason' => 'missing_dir'];
        }

        $fullPath = $baseRealPath . DIRECTORY_SEPARATOR . $safeFileName;
        $realFilePath = realpath($fullPath);
        if ($realFilePath === false || !is_file($realFilePath)) {
            return ['success' => false, 'content' => '', 'message' => 'Archivo no encontrado: ' . $safeFileName . '.', 'reason' => 'not_found'];
        }

        $basePrefix = rtrim($baseRealPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (strpos($realFilePath, $basePrefix) !== 0) {
            return ['success' => false, 'content' => '', 'message' => 'Acceso al archivo fuera del directorio permitido.', 'reason' => 'outside_dir'];
        }

        $content = file_get_contents($realFilePath);
        if (!is_string($content) || $content === '') {
            return ['success' => false, 'content' => '', 'message' => 'No fue posible leer el archivo de documentación.', 'reason' => 'read_error'];
        }

        return ['success' => true, 'content' => $content, 'message' => 'OK', 'reason' => 'local'];
    }
}
