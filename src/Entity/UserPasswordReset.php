<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserPasswordResetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserPasswordResetRepository::class)]
class UserPasswordReset implements PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     */
    #[ORM\Id()]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: "integer")]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 255)]
    private $hash;

    /**
     * @var LocalUser
     */
    #[ORM\ManyToOne(targetEntity: LocalUser::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $localuser;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 39, nullable: true)]
    private $ip;

    /**
     * @var \DateTimeInterface
     */
    #[ORM\Column(type: "datetime")]
    private $created;

    /**
     * @var boolean
     */
    #[ORM\Column(type: "boolean")]
    private $active;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->active = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->getHash();
    }

    public function setPassword(string $password): self
    {
        return $this->setHash($password);
    }

    public function getLocaluser(): ?LocalUser
    {
        return $this->localuser;
    }

    public function setLocaluser(?LocalUser $localuser): self
    {
        $this->localuser = $localuser;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
