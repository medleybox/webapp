<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MediaFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaFileRepository::class)]
class MediaFile
{
    /**
     * @var int
     */
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 36, unique: true)]
    private $uuid;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 255)]
    private $type;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['searchable'])]
    private $title = '';

    /**
     * @var int
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private $size;

    /**
     * @var float
     */
    #[ORM\Column(type: "float", nullable: true)]
    private $seconds;

    /**
     * @var ?LocalUser
     */
    #[ORM\ManyToOne(targetEntity: LocalUser::class, inversedBy: "mediaFiles", cascade: ["persist", "remove"])]
    private $importUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSeconds(): ?float
    {
        return floor($this->seconds);
    }

    public function setSeconds(?float $seconds): self
    {
        $this->seconds = $seconds;

        return $this;
    }

    public function getImportUser(): ?LocalUser
    {
        return $this->importUser;
    }

    public function setImportUser(?LocalUser $importUser): self
    {
        $this->importUser = $importUser;

        return $this;
    }
}
