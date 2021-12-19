<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User\ChangeEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Http\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ConfirmTest extends TestCase
{

    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user->requestEmailChanging($token, $now, $new = new Email('new@email.ru'));

        self::assertNotNull($user->getNewEmailToken());

        $user->confirmEmailChanging($token->getValue(), $now);

        self::assertNull($user->getNewEmailToken());
        self::assertEquals($new, $user->getEmail());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user->requestEmailChanging($token, $now, new Email('new@email.ru'));

        $this->expectExceptionMessage('Incorrect token');
        $user->confirmEmailChanging('invalid', $now);
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now);
        $user->requestEmailChanging($token, $now, new Email('new@email.ru'));

        $this->expectExceptionMessage('Incorrect expired');
        $user->confirmEmailChanging($token->getValue(), $now->modify('+1 hour'));
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('Changing is not requested');
        $user->confirmEmailChanging($token->getValue(), $now);

    }

    private function createToken(\DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}