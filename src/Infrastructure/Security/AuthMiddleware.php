<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

class AuthMiddleware
{
    private $sessionManager;
    private $basePath;

    public function __construct(SessionManager $sessionManager, string $basePath = '')
    {
        $this->sessionManager = $sessionManager;
        $this->basePath = rtrim($basePath, '/');
    }

    public function requireAuth(): void
    {
        if (!$this->sessionManager->isAuthenticated()) {
            header('Location: ' . $this->basePath . '/login');
            exit;
        }
    }
}
