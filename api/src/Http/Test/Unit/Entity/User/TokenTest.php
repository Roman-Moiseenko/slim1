<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User;


use App\Auth\Entity\User\Token;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers Id
 */
class TokenTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new Token($value = Uuid::uuid4()->toString(), $date = new \DateTimeImmutable());
        self::assertEquals($value, $token->getValue());
        self::assertEquals($date, $token->getExpires());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $token = new Token(mb_strtolower($value), new \DateTimeImmutable());
        self::assertEquals($value, $token->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('12234', new \DateTimeImmutable());
    }


    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('', new \DateTimeImmutable());
    }

    public function testValidate(): void
    {
        $token = new Token($value = Uuid::uuid4()->toString(), $date = new \DateTimeImmutable());
        $token->validate($value, $date->modify('-1 sec'));
        self::assertTrue(true);
    }

    public function testWrong(): void
    {
        $token = new Token(Uuid::uuid4()->toString(), $date = new \DateTimeImmutable());
        self::expectExceptionMessage('Incorrect token');
        $token->validate(Uuid::uuid4()->toString(), $date->modify('-1 secs'));
    }

    public function testExpired(): void
    {
        $token = new Token($value = Uuid::uuid4()->toString(), $date = new \DateTimeImmutable());
        self::expectExceptionMessage('Incorrect expired');
        $token->validate($value, $date->modify('+1 secs'));
    }
}