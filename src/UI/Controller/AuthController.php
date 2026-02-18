<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\UseCase\LoginUseCase;
use App\Application\UseCase\LogoutUseCase;
use App\Infrastructure\Security\SessionManager;

class AuthController
{
    private LoginUseCase $loginUseCase;
    private LogoutUseCase $logoutUseCase;
    private SessionManager $sessionManager;

    public function __construct(LoginUseCase $loginUseCase, LogoutUseCase $logoutUseCase, SessionManager $sessionManager)
    {
        $this->loginUseCase = $loginUseCase;
        $this->logoutUseCase = $logoutUseCase;
        $this->sessionManager = $sessionManager;
    }

    public function showLogin(?string $error = null): void
    {
        $title = 'Login';
        include __DIR__ . '/../View/login.php';
    }

    public function login(array $post): void
    {
        $username = isset($post['username']) ? (string) $post['username'] : '';
        $password = isset($post['password']) ? (string) $post['password'] : '';
        $result = $this->loginUseCase->execute($username, $password);

        if (!$result->isSuccess()) {
            $this->showLogin($result->getMessage());
            return;
        }

        $this->sessionManager->setUser((int) $result->getUserId(), (string) $result->getUsername());
        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        $this->logoutUseCase->execute();
        header('Location: /login');
        exit;
    }
}
