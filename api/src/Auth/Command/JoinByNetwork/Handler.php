<?php
declare(strict_types=1);

namespace App\Auth\Command\JoinByNetwork;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Network;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Flusher;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $identity = new Network($command->network, $command->identity);
        $email = new Email($command->email);
        if ($this->users->hasByNetwork($identity))
            throw new \DomainException('User already exist');

        if ($this->users->hasByEmail(new Email($command->email)))
            throw new \DomainException('User already exist');

        $user = User::requestJoinByNetwork(
            Id::generate(),
            new \DateTimeImmutable(),
            $email,
            $identity
        );
        $this->users->add($user);
        $this->flusher->flush();
    }
}