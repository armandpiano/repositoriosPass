<?php

declare(strict_types=1);

namespace App\Infrastructure\DocFetcher;

class DocxDownloader
{
    private $projectRoot;
    private $directories;

    public function __construct(string $projectRoot, string $documentationDir, string $functionalityDir)
    {
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
        $this->directories = [
            'doc' => trim($documentationDir),
            'func' => trim($functionalityDir),
        ];
    }

    public function downloadByType(string $source, string $docType): array
    {
        if (!array_key_exists($docType, $this->directories)) {
            return ['success' => false, 'content' => '', 'message' => 'Tipo de documento no soportado.', 'reason' => 'invalid_type'];
        }

        if (trim($source) === '') {
            return ['success' => false, 'content' => '', 'message' => 'El nombre del archivo no está configurado.', 'reason' => 'empty'];
        }

        return $this->downloadLocal($source, $this->directories[$docType], $docType);
    }

    private function downloadLocal(string $docFileName, string $directoryName, string $docType): array
    {
        $safeFileName = basename(trim($docFileName));
        if ($safeFileName === '' || $safeFileName !== trim($docFileName) || strpos($safeFileName, '..') !== false) {
            return ['success' => false, 'content' => '', 'message' => 'El nombre del archivo de ' . $docType . ' no es válido.', 'reason' => 'invalid_filename'];
        }

        $basePath = $this->projectRoot . DIRECTORY_SEPARATOR . trim($directoryName, '/\\');
        $baseRealPath = realpath($basePath);
        if ($baseRealPath === false || !is_dir($baseRealPath)) {
            return ['success' => false, 'content' => '', 'message' => 'No se encontró la carpeta para ' . $docType . '.', 'reason' => 'missing_dir'];
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
            return ['success' => false, 'content' => '', 'message' => 'No fue posible leer el archivo.', 'reason' => 'read_error'];
        }

        return ['success' => true, 'content' => $content, 'message' => 'OK', 'reason' => 'local'];
    }
}
