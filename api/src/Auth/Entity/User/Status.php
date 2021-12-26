<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

class Status
{
    private const WAIT = 'wait';
    private const ACTIVE = 'active';
    private string $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::WAIT,
            self::ACTIVE
        ]);
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

    public function getName(): string
    {
        return $this->value;
    }

}