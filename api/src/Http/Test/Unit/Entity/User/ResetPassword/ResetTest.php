<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User\ResetPassword;

use App\Auth\Entity\User\Token;
use App\Http\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestPasswordReset($token, $now);
        self::assertNotNull($user->getPasswordResetToken());
        $user->resetPassword($token->getValue(), $now, $hash = 'hash');

        self::assertNull($user->getPasswordResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Incorrect token');
        $user->resetPassword(Uuid::uuid4()->toString(), $now, 'hash');
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Incorrect expired');
        $user->resetPassword($token->getValue(), $now->modify('+2 hour'), 'hash');
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->active()->build();
        $now = new \DateTimeImmutable();

        $this->expectExceptionMessage('Resetting is not requested');
        $user->resetPassword(Uuid::uuid4()->toString(), $now, 'hash');
    }


    private function createToken(\DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}