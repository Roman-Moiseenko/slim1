<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use DomainException;

interface UserRepository
{
    public function hasByEmail(string $email): bool;

    public function add(User $user);

    public function findByConfirmToken(string $token): ?User;

    public function hasByNetwork(NetworkIdentity $identity): bool;

    /**
     * @param Id $id
     * @return User
     * @throws DomainException
     */
    public function get(Id $id): User;

    /**
     * @param Email $email
     * @return User
     * @throws DomainException
     */
    public function getByEmail(Email $email): User;
    public function findByPasswordResetToken(string $token): ?User;
}