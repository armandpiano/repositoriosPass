<?php

declare(strict_types=1);

namespace App\Application\DTO;

class LoginResult
{
    private bool $success;
    private string $message;
    private ?int $userId;
    private ?string $username;

    public function __construct(bool $success, string $message, ?int $userId = null, ?string $username = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->userId = $userId;
        $this->username = $username;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
