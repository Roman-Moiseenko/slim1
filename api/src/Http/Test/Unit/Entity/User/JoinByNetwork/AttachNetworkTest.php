<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User\JoinByNetwork;

use App\Auth\Entity\User\Network;
use App\Http\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

class AttachNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();
        $network = new Network('vk', '0001');
        $user->attachNetwork($network);

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertEquals($network, $networks[0] ?? null);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->active()->build();
        $network = new Network('vk', '0001');
        $user->attachNetwork($network);
        self::expectExceptionMessage('Network is already attached');
        $user->attachNetwork($network);

    }
}