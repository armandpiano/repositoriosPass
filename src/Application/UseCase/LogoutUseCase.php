<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Infrastructure\Security\SessionManager;

class LogoutUseCase
{
    private $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function execute(): void
    {
        $this->sessionManager->destroy();
    }
}
