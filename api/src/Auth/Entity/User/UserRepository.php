<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

interface UserRepository
{
    public function hasByEmail(string $email): bool;

    public function add(User $user);

    public function findByConfirmToken(string $token): ?User;

    public function hasByNetwork(NetworkIdentity $identity): bool;

    public function get(Id $id): ?User;
}