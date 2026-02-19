<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\UseCase\LoginUseCase;
use App\Application\UseCase\LogoutUseCase;
use App\Infrastructure\Security\SessionManager;

class AuthController
{
    private $loginUseCase;
    private $logoutUseCase;
    private $sessionManager;
    private $basePath;

    public function __construct(LoginUseCase $loginUseCase, LogoutUseCase $logoutUseCase, SessionManager $sessionManager, string $basePath = '')
    {
        $this->loginUseCase = $loginUseCase;
        $this->logoutUseCase = $logoutUseCase;
        $this->sessionManager = $sessionManager;
        $this->basePath = rtrim($basePath, '/');
    }

    public function showLogin(?string $error = null): void
    {
        $title = 'Login';
        $basePath = $this->basePath;
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
        header('Location: ' . $this->basePath . '/dashboard');
        exit;
    }

    public function logout(): void
    {
        $this->logoutUseCase->execute();
        header('Location: ' . $this->basePath . '/login');
        exit;
    }
}
