<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Project
{
    private $id;
    private $name;
    private $company;
    private $projectUrl;
    private $docFilename;
    private $funcFilename;
    private $videoFilename;
    private $createdAt;

    public function __construct(
        int $id,
        string $name,
        string $company,
        string $projectUrl,
        string $docFilename,
        string $funcFilename,
        string $videoFilename,
        \DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->company = $company;
        $this->projectUrl = $projectUrl;
        $this->docFilename = $docFilename;
        $this->funcFilename = $funcFilename;
        $this->videoFilename = $videoFilename;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getProjectUrl(): string
    {
        return $this->projectUrl;
    }

    public function getDocFilename(): string
    {
        return $this->docFilename;
    }

    public function getFuncFilename(): string
    {
        return $this->funcFilename;
    }

    public function getVideoFilename(): string
    {
        return $this->videoFilename;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
