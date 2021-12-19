<?php
declare(strict_types=1);

namespace App\Http\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\Status;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private string $passwordHash;
    private ?Token $joinConfirmToken;
    private Status $status;
    private bool $active = false;
    private ?NetworkIdentity $networkIdentity = null;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->date = new \DateTimeImmutable();
        $this->email = new Email('email@test.ru');
        $this->passwordHash = 'hash';
        $this->joinConfirmToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmToken = $token;
        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): User
    {
        if ($this->networkIdentity === null) {
            $user = User::requestJoinByEmail(
                $this->id,
                $this->date,
                $this->email,
                $this->passwordHash,
                $this->joinConfirmToken
            );
        } else {
            $user = User::requestJoinByNetwork(
                $this->id,
                $this->date,
                $this->email,
                $this->networkIdentity
            );
        }
        if ($this->active) {
            $user->confirmJoin(
                $this->joinConfirmToken->getValue(),
                $this->joinConfirmToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }

    public function viaNetwork(NetworkIdentity $identity = null): UserBuilder
    {
        $clone = clone $this;
        $clone->networkIdentity = $identity ?? new NetworkIdentity('vk', '0001');
        return $clone;
    }

    public function withEmail(Email $email): UserBuilder
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }
}