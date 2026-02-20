<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class ProjectDoc
{
    private $id;
    private $projectId;
    private $docType;
    private $htmlContent;
    private $fetchedAt;
    private $hash;

    public function __construct(int $id, int $projectId, string $docType, string $htmlContent, \DateTimeImmutable $fetchedAt, string $hash)
    {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->docType = $docType;
        $this->htmlContent = $htmlContent;
        $this->fetchedAt = $fetchedAt;
        $this->hash = $hash;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getDocType(): string
    {
        return $this->docType;
    }

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    public function getFetchedAt(): \DateTimeImmutable
    {
        return $this->fetchedAt;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
