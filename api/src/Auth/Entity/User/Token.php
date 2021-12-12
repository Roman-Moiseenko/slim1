<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Token
{
    private \DateTimeImmutable $expires;
    private string $value;

    public function __construct(string $value, \DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value = $value;
        $this->expires = $expires;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): \DateTimeImmutable
    {
        return $this->expires;
    }

    public function validate(string $token, \DateTimeImmutable $date)
    {
        if ($this->value !== $token)
            throw new \DomainException('Incorrect token');
        if ($this->expires < $date)
            throw new \DomainException('Incorrect expired');
    }
}