<?php

declare(strict_types=1);

namespace App\Domain\Port;

use App\Domain\Entity\User;

interface UserRepository
{
    public function findByUsername(string $username): ?User;
}
