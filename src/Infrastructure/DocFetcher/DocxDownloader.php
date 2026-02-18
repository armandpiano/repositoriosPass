<?php

declare(strict_types=1);

namespace App\Infrastructure\DocFetcher;

class DocxDownloader
{
    public function download(string $url): array
    {
        if (trim($url) === '') {
            return ['success' => false, 'content' => '', 'message' => 'La URL del documento no esta configurada.'];
        }

        $ch = curl_init($url);
        if ($ch === false) {
            return ['success' => false, 'content' => '', 'message' => 'No fue posible iniciar la descarga del documento.'];
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
            return ['success' => false, 'content' => '', 'message' => 'No se pudo descargar el documento. ' . ($curlError !== '' ? 'Detalle: ' . $curlError : '')];
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return ['success' => false, 'content' => '', 'message' => 'No se pudo descargar el documento (HTTP ' . $httpCode . ').'];
        }

        return ['success' => true, 'content' => $content, 'message' => 'OK'];
    }
}
