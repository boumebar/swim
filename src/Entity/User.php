<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"), // Lecture réservée aux admins
        new Post(security: "is_granted('ROLE_ADMIN')"), // Création réservée aux admins
        new Get(uriTemplate: '/users/{id}', security: "is_granted('ROLE_ADMIN')", requirements: ['id' => '\d+'],), // Lecture d'un utilisateur réservée aux admins
        new Put(security: "is_granted('ROLE_ADMIN')", extraProperties: ["standard_put" => true]), // Remplacement réservé aux admins
        new Delete(security: "is_granted('ROLE_ADMIN')"), // Suppression réservée aux admins
        new Patch(security: "is_granted('ROLE_ADMIN')") // Mise à jour partielle réservée aux admins
    ]
)]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $password = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'loueur', cascade: ['remove'])]
    private Collection $reservations;

    /**
     * @var Collection<int, Pool>
     */
    #[ORM\OneToMany(targetEntity: Pool::class, mappedBy: 'owner', cascade: ['remove'])]
    private Collection $pools;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->pools = new ArrayCollection();
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

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

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
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Pool>
     */
    public function getPools(): Collection
    {
        return $this->pools;
    }

    public function addPools(Pool $pools): static
    {
        if (!$this->pools->contains($pools)) {
            $this->pools->add($pools);
            $pools->setOwner($this);
        }

        return $this;
    }

    public function removePools(Pool $pools): static
    {
        if ($this->pools->removeElement($pools)) {
            // set the owning side to null (unless already changed)
            if ($pools->getOwner() === $this) {
                $pools->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservations(Reservation $reservations): static
    {
        if (!$this->reservations->contains($reservations)) {
            $this->reservations->add($reservations);
            $reservations->setLoueur($this);
        }

        return $this;
    }

    public function removeReservations(Reservation $reservations): static
    {
        if ($this->reservations->removeElement($reservations)) {
            // set the owning side to null (unless already changed)
            if ($reservations->getLoueur() === $this) {
                $reservations->setLoueur(null);
            }
        }

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
}
