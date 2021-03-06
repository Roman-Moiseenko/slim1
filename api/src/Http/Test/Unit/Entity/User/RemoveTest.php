<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User;

use App\Http\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

class RemoveTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->build();
        $user->remove();
    }

    public function testActive(): void
    {
        $user = (new UserBuilder())->active()->build();
        $this->expectExceptionMessage('Unable to remove active user');
        $user->remove();
    }
}