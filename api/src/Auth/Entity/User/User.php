<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;

class User
{


    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private ?string $passwordHash = null;
    private ?Token $joinConfirmToken = null;
    private Status $status;
    private \ArrayObject $networks;
    private ?Token $passwordResetToken = null;
    //private NetworkIdentity $network;

    public function __construct(
        Id                 $id,
        \DateTimeImmutable $date,
        Email              $email,
        Status             $status
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->networks = new \ArrayObject();
    }

    public static function requestJoinByEmail(Id                 $id,
                                              \DateTimeImmutable $date,
                                              Email              $email,
                                              string             $passwordHash,
                                              Token              $joinConfirmToken): self
    {
        $user = new User($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->joinConfirmToken = $joinConfirmToken;
        return $user;
    }

    public static function requestJoinByNetwork(Id                 $id,
                                                \DateTimeImmutable $date,
                                                Email              $email,
                                                NetworkIdentity    $network): self
    {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->append($network);
        return $user;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }


    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return Token|null
     */
    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function confirmJoin(string $token, \DateTimeImmutable $date)
    {

        if ($this->joinConfirmToken === null)
            throw new \DomainException('Confirmation is not required');

        $this->joinConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmToken = null;

    }

    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return $this->networks->getArrayCopy();
    }

    public function attachNetwork(NetworkIdentity $identity)
    {
        /** @var NetworkIdentity $existing */
        foreach ($this->networks as $existing){
            if ($existing->isEqualTo($identity))
                throw new \DomainException('Network is already attached');
        }
        $this->networks->append($identity);
    }

    public function requestPasswordReset(Token $token, \DateTimeImmutable $date)
    {
        if (!$this->isActive()) throw new \DomainException('User is not active');
        if ($this->passwordResetToken != null && !$this->passwordResetToken->isExpiredTo($date))
            throw new \DomainException('Resetting is already requested');
        $this->passwordResetToken = $token;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function resetPassword(string $token, \DateTimeImmutable $date, string $hash)
    {
        if ($this->passwordResetToken === null) throw new \DomainException('Resetting is not requested');
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher)
    {
        if ($this->passwordHash === null) throw new \DomainException('User does not have an old password');
        if (!$hasher->validate($current, $this->getPasswordHash())) throw new \DomainException('Incorrect current password');
        $this->passwordHash = $hasher->hash($new);
    }
}