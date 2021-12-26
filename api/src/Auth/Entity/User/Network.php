<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

class Network
{
    private string $name;
    private string $identity;

    public function __construct(string $network, string $identity)
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);
        $this->name = mb_strtolower($network);
        $this->identity = mb_strtolower($identity);
    }

    #[Pure] public function isEqualTo(self $network): bool
    {
        return $this->getName() === $network->getName() &&
            $this->getIdentity() === $network->getIdentity();
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }


}