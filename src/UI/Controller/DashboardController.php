<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Infrastructure\Security\SessionManager;

class DashboardController
{
    private $sessionManager;
    private $basePath;

    public function __construct(SessionManager $sessionManager, string $basePath = '')
    {
        $this->sessionManager = $sessionManager;
        $this->basePath = rtrim($basePath, '/');
    }

    public function index(): void
    {
        $title = 'Dashboard';
        $user = $this->sessionManager->getUser();
        $basePath = $this->basePath;
        include __DIR__ . '/../View/dashboard.php';
    }
}
