<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\LoginResult;
use App\Domain\Port\UserRepository;

class LoginUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(string $username, string $password): LoginResult
    {
        $username = trim($username);
        if ($username === '' || $password === '') {
            return new LoginResult(false, 'Debes ingresar usuario y contrasena.');
        }

        $user = $this->userRepository->findByUsername($username);
        if ($user === null || !password_verify($password, $user->getPasswordHash())) {
            return new LoginResult(false, 'Credenciales invalidas.');
        }

        return new LoginResult(true, 'Login exitoso.', $user->getId(), $user->getUsername());
    }
}
