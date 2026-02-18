<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Infrastructure\Security\SessionManager;

class DashboardController
{
    private $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function index(): void
    {
        $title = 'Dashboard';
        $user = $this->sessionManager->getUser();
        include __DIR__ . '/../View/dashboard.php';
    }
}
