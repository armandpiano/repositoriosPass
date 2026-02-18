<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

class AuthMiddleware
{
    private $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function requireAuth(): void
    {
        if (!$this->sessionManager->isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }
}
