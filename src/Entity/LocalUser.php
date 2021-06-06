<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocalUserRepository")
 */
class LocalUser implements UserInterface
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @var array<string> $roles
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<int, MediaFile>
     * @ORM\OneToMany(targetEntity=MediaFile::class, mappedBy="importUser")
     */
    private $mediaFiles;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var boolean
     * By default users are inactive
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    public function __construct()
    {
        $this->mediaFiles = new ArrayCollection();
    }

    public function __toString(): ?string
    {
        return $this->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param array< string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?bool
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): bool
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;

        return true;
    }

    /**
     * @return Collection<int, MediaFile>
     */
    public function getMediaFiles(): Collection
    {
        return $this->mediaFiles;
    }

    public function addMediaFile(MediaFile $mediaFile): self
    {
        if (!$this->mediaFiles->contains($mediaFile)) {
            $this->mediaFiles[] = $mediaFile;
            $mediaFile->setImportUser($this);
        }

        return $this;
    }

    public function removeMediaFile(MediaFile $mediaFile): self
    {
        if ($this->mediaFiles->removeElement($mediaFile)) {
            // set the owning side to null (unless already changed)
            if ($mediaFile->getImportUser() === $this) {
                $mediaFile->setImportUser(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
