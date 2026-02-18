<?php

declare(strict_types=1);

namespace App\Infrastructure\DocFetcher;

use PhpOffice\PhpWord\IOFactory;

class DocxToHtmlConverter
{
    public function convertToHtml(string $binaryDocx): string
    {
        $tempDocx = tempnam(sys_get_temp_dir(), 'docx_');
        if ($tempDocx === false) {
            throw new \RuntimeException('No se pudo crear archivo temporal.');
        }

        $tempDocxFile = $tempDocx . '.docx';
        rename($tempDocx, $tempDocxFile);
        file_put_contents($tempDocxFile, $binaryDocx);

        $phpWord = IOFactory::load($tempDocxFile, 'Word2007');
        $writer = IOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $writer->save('php://output');
        $html = ob_get_clean();

        @unlink($tempDocxFile);

        return is_string($html) ? $html : '';
    }
}
