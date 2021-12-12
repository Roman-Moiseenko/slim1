<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

class User
{


    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private ?string $passwordHash = null;
    private ?Token $joinConfirmToken = null;
    private Status $status;
    private \ArrayObject $networks;
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
}