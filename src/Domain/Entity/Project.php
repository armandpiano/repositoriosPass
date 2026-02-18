<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Project
{
    private $id;
    private $name;
    private $projectUrl;
    private $docxUrl;
    private $createdAt;

    public function __construct(int $id, string $name, string $projectUrl, string $docxUrl, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->projectUrl = $projectUrl;
        $this->docxUrl = $docxUrl;
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

    public function getProjectUrl(): string
    {
        return $this->projectUrl;
    }

    public function getDocxUrl(): string
    {
        return $this->docxUrl;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
