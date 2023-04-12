<?php

namespace App\Entity;

use App\Repository\MediaCollectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaCollectionRepository::class)]
class MediaCollection
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    /**
     * @var LocalUser
     */
    #[ORM\ManyToOne(targetEntity: LocalUser::class, inversedBy: 'mediaCollections')]
    private $localUser;

    /**
     * @var int
     */
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

    public function getLocalUser(): ?LocalUser
    {
        return $this->localUser;
    }

    public function setLocalUser(?LocalUser $localUser): self
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
