<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

class SessionManager
{
    private $sessionName;

    public function __construct(string $sessionName)
    {
        $this->sessionName = $sessionName;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name($this->sessionName);
            session_start();
        }
    }

    public function setUser(int $id, string $username): void
    {
        $_SESSION['user'] = [
            'id' => $id,
            'username' => $username,
        ];
    }

    public function getUser(): ?array
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    public function isAuthenticated(): bool
    {
        return $this->getUser() !== null;
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
    }
}
