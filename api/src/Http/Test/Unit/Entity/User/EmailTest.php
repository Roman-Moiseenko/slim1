<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Entity\User;


use App\Auth\Entity\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers Email
 */
class EmailTest extends TestCase
{
    public function testSuccess(): void
    {
        $email = new Email($value = 'email@test.ru');
        self::assertEquals($value, $email->getValue());
    }

    public function testCase(): void
    {
        $email = new Email($value = 'EmaIl@teST.ru');
        self::assertEquals('email@test.ru', $email->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not-email');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }
}