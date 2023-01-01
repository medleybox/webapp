<?php

namespace App\Entity;

use App\Repository\LocalUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: LocalUserRepository::class)]
#[UniqueEntity(fields: "username", message: "This username is already taken.")]
#[UniqueEntity(fields: "email", message: "An account with this email already exists.")]
class LocalUser implements UserInterface, PasswordAuthenticatedUserInterface
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
    #[ORM\Column(type: "string", length: 180, unique: true)]
    private $username;

    /**
     * @var array<string> $roles
     */
    #[ORM\Column(type: "json")]
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: "string")]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 8, max: 4096)]
    private $password;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<int, MediaFile>
     */
    #[ORM\OneToMany(targetEntity: MediaFile::class, mappedBy: "importUser")]
    private $mediaFiles;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 255, unique: true)]
    private $email;

    /**
     * @var boolean
     * By default users are inactive
     */
    #[ORM\Column(type: "boolean")]
    private $active = false;

    /**
     * @var ?UserSettings
     * By default users are inactive
     */
    #[ORM\OneToOne(targetEntity: UserSettings::class, mappedBy: "ref", cascade: ["persist", "remove"])]
    private $settings;

    #[ORM\OneToMany(mappedBy: 'localUser', targetEntity: UserPlayHistory::class, orphanRemoval: true)]
    private $userPlayHistories;

    #[ORM\OneToMany(mappedBy: 'localUser', targetEntity: MediaCollection::class)]
    private $mediaCollections;

    public function __construct()
    {
        $this->mediaFiles = new ArrayCollection();
        $this->mediaCollections = new ArrayCollection();
    }

    public function __toString(): string
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

    public function hasRole(string $role): bool
    {
        if (null === $this->roles) {
            return false;
        }

        foreach ($this->roles as $r) {
            if ($r === $role) {
                return true;
            }
        }

        return false;
    }

    public function setRole(string $role): self
    {
        if (false === $this->hasRole($role)) {
            $roles = $this->roles;
            $roles[] = $role;
            $this->setRoles($roles);
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (true === $this->hasRole($role)) {
            $roles = $this->getRoles();
            unset($roles[$role]);
            $this->setRoles($roles);
        }

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
     * @see UserInterface
     */
    public function getUserIdentifier(): bool
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

    public function getSettings(): UserSettings
    {
        if (null === $this->settings) {
            $this->settings = new UserSettings();
        }

        return $this->settings;
    }

    public function setSettings(UserSettings $settings): self
    {
        // set the owning side of the relation if necessary
        if ($settings->getRef() !== $this) {
            $settings->setRef($this);
        }

        $this->settings = $settings;

        return $this;
    }

    /**
     * @return Collection<int, UserPlayHistory>
     */
    public function getUserPlayHistories(): Collection
    {
        return $this->userPlayHistories;
    }

    public function addUserPlayHistory(UserPlayHistory $userPlayHistory): self
    {
        if (!$this->userPlayHistories->contains($userPlayHistory)) {
            $this->userPlayHistories[] = $userPlayHistory;
            $userPlayHistory->setLocalUser($this);
        }

        return $this;
    }

    public function removeUserPlayHistory(UserPlayHistory $userPlayHistory): self
    {
        if ($this->userPlayHistories->removeElement($userPlayHistory)) {
            // set the owning side to null (unless already changed)
            if ($userPlayHistory->getLocalUser() === $this) {
                $userPlayHistory->setLocalUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MediaCollection>
     */
    public function getMediaCollections(): Collection
    {
        return $this->mediaCollections;
    }

    public function addMediaCollection(MediaCollection $mediaCollection): self
    {
        if (!$this->mediaCollections->contains($mediaCollection)) {
            $this->mediaCollections[] = $mediaCollection;
            $mediaCollection->setLocalUser($this);
        }

        return $this;
    }

    public function removeMediaCollection(MediaCollection $mediaCollection): self
    {
        if ($this->mediaCollections->removeElement($mediaCollection)) {
            // set the owning side to null (unless already changed)
            if ($mediaCollection->getLocalUser() === $this) {
                $mediaCollection->setLocalUser(null);
            }
        }

        return $this;
    }
}
