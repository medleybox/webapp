<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserSettingsRepository::class)
 */
class UserSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=LocalUser::class, inversedBy="settings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $ref;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $autoPlay = false;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $random = false;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $openVlc = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?LocalUser
    {
        return $this->ref;
    }

    public function setRef(LocalUser $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function isAutoPlay(): ?bool
    {
        return $this->autoPlay;
    }

    public function setAutoPlay(bool $autoPlay): self
    {
        $this->autoPlay = $autoPlay;

        return $this;
    }

    public function israndom(): ?bool
    {
        return $this->random;
    }

    public function setrandom(bool $random): self
    {
        $this->random = $random;

        return $this;
    }

    public function isOpenVlc(): ?bool
    {
        return $this->openVlc;
    }

    public function setOpenVlc(bool $openVlc): self
    {
        $this->openVlc = $openVlc;

        return $this;
    }
}
