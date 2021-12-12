<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use JetBrains\PhpStorm\Pure;

class Status
{
    private const WAIT = 'wait';
    private const ACTIVE = 'active';
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function wait(): self
    {
        return new static(self::WAIT);
    }

    public static function active(): self
    {
        return new static(self::ACTIVE);
    }

    public function isWait(): bool
    {
        return $this->value === self::WAIT;
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

}