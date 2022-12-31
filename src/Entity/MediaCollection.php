<?php

namespace App\Entity;

use App\Repository\MediaCollectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaCollectionRepository::class)]
class MediaCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: localUser::class, inversedBy: 'mediaCollections')]
    private $localUser;

    #[ORM\Column(type: 'integer')]
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLocalUser(): ?localUser
    {
        return $this->localUser;
    }

    public function setLocalUser(?localUser $localUser): self
    {
        $this->localUser = $localUser;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
