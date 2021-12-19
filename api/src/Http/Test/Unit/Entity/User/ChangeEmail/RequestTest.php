<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User\ChangeEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Http\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old@email.com'))
            ->active()
            ->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user->requestEmailChanging($token, $now, $new = new Email('new@email.ru'));

        self::assertNotNull($user->getNewEmailToken());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($new, $user->getNewEmail());
    }

    public function testSame(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old@email.com'))
            ->active()
            ->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $this->expectExceptionMessage('Email is already same');
        $user->requestEmailChanging($token, $now, $old);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user->requestEmailChanging($token, $now, $email = new Email('new@email.ru'));
        $this->expectExceptionMessage('Changing is already requested');
        $user->requestEmailChanging($token, $now, $email);

    }
    public function testExpired(): void
    {
        $user = (new UserBuilder())->active()->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestEmailChanging($token, $now, new Email('new@email.ru'));

        $newDate = $now->modify('+2 hours');
        $newToken = $this->createToken($newDate->modify('+1 hour'));
        $user->requestEmailChanging($newToken, $newDate, $newEmail = new Email('new2@email.ru'));

        self::assertEquals($newToken, $user->getNewEmailToken());
        self::assertEquals($newEmail, $user->getNewEmail());
    }

    public function nestNotActive(): void
    {
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user = (new UserBuilder())->build();
        $this->expectExceptionMessage('User is not active');
        $user->requestEmailChanging($token, $now, new Email('new@email.ru'));

    }

    private function createToken(\DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}