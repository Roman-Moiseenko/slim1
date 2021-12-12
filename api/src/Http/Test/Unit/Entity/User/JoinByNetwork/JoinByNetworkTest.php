<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User\JoinByNetwork;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\User;
use PHPUnit\Framework\TestCase;

class JoinByNetworkTest extends TestCase
{
    public function testSucces()
    {
        $user = User::requestJoinByNetwork(
            $id = Id::generate(),
            $date = new \DateTimeImmutable(),
            $email = new Email('test@email.ru'),
            $network = new NetworkIdentity('vk', '0001')
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertEquals($network, $networks[0] ?? null);

    }
}