<?php
declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use ArrayObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="auth_users")
 */
class User
{

    /**
     * @ORM\Column(type="auth_user_id")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @ORM\Column(type="date_immutable")
     */
    private \DateTimeImmutable $date;
    /**
     * @ORM\Column(type="auth_user_email", unique=true)
     */
    private Email $email;
    /**
     * @ORM\Column (type="string", name="password_hash", nullable=true)
     */
    private ?string $passwordHash = null;

    #[ORM\Embedded(class: Token::class)]
    private ?Token $joinConfirmToken = null;
    /**
     * @ORM\Column (type="auth_user_status", length=16)
     */
    private Status $status;
    #[ORM\Embedded(class: Token::class)]
    private ?Token $passwordResetToken = null;
    #[ORM\Embedded(class: Token::class)]
    private ?Token $newEmailToken = null;
    /**
     * @ORM\Column(type="auth_user_email", nullable=true)
     */
    private ?Email $newEmail = null;
    /**
     * @ORM\Column (type="auth_user_role", length=16)
     */
    private Role $role;
    /**
     * @ORM\OneToMany(targetEntity="UserNetwork", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $networks;

    public function __construct(
        Id                 $id,
        \DateTimeImmutable $date,
        Email              $email,
        Status             $status
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function requestJoinByEmail(Id                 $id,
                                              \DateTimeImmutable $date,
                                              Email              $email,
                                              string             $passwordHash,
                                              Token              $joinConfirmToken): self
    {
        $user = new User($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->joinConfirmToken = $joinConfirmToken;
        return $user;
    }

    public static function requestJoinByNetwork(Id                 $id,
                                                \DateTimeImmutable $date,
                                                Email              $email,
                                                Network            $network): self
    {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->add(new UserNetwork($user, $network));
        return $user;
    }

    #[Pure] public function isWait(): bool
    {
        return $this->status->isWait();
    }

    #[Pure] public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }


    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return Token|null
     */
    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function confirmJoin(string $token, \DateTimeImmutable $date)
    {

        if ($this->joinConfirmToken === null)
            throw new \DomainException('Confirmation is not required');

        $this->joinConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmToken = null;

    }

    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return $this->networks->map(static function (UserNetwork $network) {
            return $network->getNetwork();
        })->toArray();
    }

    public function attachNetwork(Network $identity)
    {
        /** @var UserNetwork $existing */
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($identity))
                throw new \DomainException('Network is already attached');
        }
        $this->networks->add(new UserNetwork($this, $identity));
    }

    public function requestPasswordReset(Token $token, \DateTimeImmutable $date)
    {
        if (!$this->isActive()) throw new \DomainException('User is not active');
        if ($this->passwordResetToken != null && !$this->passwordResetToken->isExpiredTo($date))
            throw new \DomainException('Resetting is already requested');
        $this->passwordResetToken = $token;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function resetPassword(string $token, \DateTimeImmutable $date, string $hash)
    {
        if ($this->passwordResetToken === null) throw new \DomainException('Resetting is not requested');
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher)
    {
        if ($this->passwordHash === null) throw new \DomainException('User does not have an old password');
        if (!$hasher->validate($current, $this->getPasswordHash())) throw new \DomainException('Incorrect current password');
        $this->passwordHash = $hasher->hash($new);
    }

    public function requestEmailChanging(Token $token, \DateTimeImmutable $date, Email $email)
    {
        if (!$this->isActive()) throw new \DomainException('User is not active');
        if ($this->email->isEqualTo($email)) throw new \DomainException('Email is already same');
        if ($this->newEmailToken !== null && !$this->newEmailToken->isExpiredTo($date)) throw new \DomainException('Changing is already requested');
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    /**
     * @return Email|null
     */
    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    /**
     * @return Token|null
     */
    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function confirmEmailChanging(string $token, \DateTimeImmutable $date): void
    {
        if ($this->newEmail === null || $this->newEmailToken === null) throw new \DomainException('Changing is not requested');
        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    public function changeRole(Role $role)
    {
        $this->role = $role;
    }

    public function remove()
    {
        if (!$this->isWait()) throw new \DomainException('Unable to remove active user');
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds()
    {
        if ($this->joinConfirmToken && $this->joinConfirmToken->isEmpty()) $this->joinConfirmToken = null;
        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) $this->passwordResetToken = null;
        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) $this->newEmailToken = null;

    }
}