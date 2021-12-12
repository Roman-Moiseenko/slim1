<?php
declare(strict_types=1);

namespace App\Http\Test\Unit\Service;

use App\Auth\Service\PasswordHasher;
use PHPUnit\Framework\TestCase;

class PasswordHasherTest extends TestCase
{
    public function testHash(): void
    {
        $hasher = new PasswordHasher(16);
        $hash = $hasher->hash($password = 'password');
        self::assertNotEmpty($hash);
        self::assertNotEquals($password, $hash);
    }

    public function testHashEmpty(): void
    {
        $hasher = new PasswordHasher(16);
        $this->expectException(\InvalidArgumentException::class);
        $hasher->hash('');
    }

    public function testValidate(): void
    {
        $hasher = new PasswordHasher(16);
        $hash = $hasher->hash($password = 'password');
        self::assertTrue($hasher->validate($password, $hash));
        self::assertFalse($hasher->validate('$password$', $hash));
    }
}