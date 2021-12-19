<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

class Email
{

    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::email($value);
        $this->value = mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    #[Pure] public function isEqualTo(Email $email): bool
    {
        return $this->value === $email->getValue();
    }
}