<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable()
 */
class Token
{
    /**
     * @var string
     * @ORM\Column (type="string", nullable=true)
     */
    private $value;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column (type="date_immutable", nullable=true)
     */
    private  $expires;

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
        if (!$this->isEqualTo($token))
            throw new \DomainException('Incorrect token');
        if ($this->isExpiredTo($date))
            throw new \DomainException('Incorrect expired');
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    private function isEqualTo(string $token): bool
    {
        return $this->value == $token;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }
}