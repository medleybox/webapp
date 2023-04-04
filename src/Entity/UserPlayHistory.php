<?php

namespace App\Entity;

use App\Repository\UserPlayHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserPlayHistoryRepository::class)]
class UserPlayHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: MediaFile::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $media;

    #[ORM\ManyToOne(targetEntity: LocalUser::class, inversedBy: 'userPlayHistories', cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(nullable: false)]
    private $localUser;

    #[ORM\Column(type: 'datetime')]
    private $added;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $completed;


    #[ORM\PrePersist]
    public function setAddedValue(): void
    {
        $this->added = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedia(): ?MediaFile
    {
        return $this->media;
    }

    public function setMedia(?MediaFile $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getLocalUser(): ?LocalUser
    {
        return $this->localUser;
    }

    public function setLocalUser(?LocalUser $localUser): self
    {
        $this->localUser = $localUser;

        return $this;
    }

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    public function getCompleted(): ?int
    {
        return $this->completed;
    }

    public function setCompleted(?int $completed): self
    {
        $this->completed = $completed;

        return $this;
    }
}
