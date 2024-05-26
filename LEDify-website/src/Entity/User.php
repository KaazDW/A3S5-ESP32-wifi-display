<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\OneToMany(targetEntity: Led::class, mappedBy: 'user')]
    private Collection $leds;

    #[ORM\OneToMany(targetEntity: Display::class, mappedBy: 'user')]
    private Collection $displays;

    public function __construct()
    {
        $this->leds = new ArrayCollection();
        $this->displays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Led>
     */
    public function getLeds(): Collection
    {
        return $this->leds;
    }

    public function addLed(Led $led): static
    {
        if (!$this->leds->contains($led)) {
            $this->leds->add($led);
            $led->setUser($this);
        }

        return $this;
    }

    public function removeLed(Led $led): static
    {
        if ($this->leds->removeElement($led)) {
            // set the owning side to null (unless already changed)
            if ($led->getUser() === $this) {
                $led->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Display>
     */
    public function getDisplays(): Collection
    {
        return $this->displays;
    }

    public function addDisplay(Display $display): static
    {
        if (!$this->displays->contains($display)) {
            $this->displays->add($display);
            $display->setUser($this);
        }

        return $this;
    }

    public function removeDisplay(Display $display): static
    {
        if ($this->displays->removeElement($display)) {
            // set the owning side to null (unless already changed)
            if ($display->getUser() === $this) {
                $display->setUser(null);
            }
        }

        return $this;
    }
}
