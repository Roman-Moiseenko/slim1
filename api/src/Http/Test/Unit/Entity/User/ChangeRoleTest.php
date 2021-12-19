<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User;

use App\Auth\Entity\User\Role;
use App\Http\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

class ChangeRoleTest extends TestCase
{
    public function testSucces(): void
    {
        $user = (new UserBuilder())->build();
        $user->changeRole($role = new Role(Role::ADMIN));

        self::assertEquals($role, $user->getRole());
    }
}