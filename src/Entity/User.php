<?php

namespace App\Entity;

use App\State\MeProvider;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"), // Lecture réservée aux admins
        new Post(security: "is_granted('ROLE_ADMIN')"), // Création réservée aux admins
        new Get(uriTemplate: '/users/{id}', security: "is_granted('ROLE_ADMIN')", requirements: ['id' => '\d+'],), // Lecture d'un utilisateur réservée aux admins
        new Get(
            uriTemplate: '/me',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            provider: MeProvider::class,
            openapiContext: [
                'summary' => 'Get the authenticated user',
                'description' => 'Retrieves the current logged in user',
            ]
        ),
        new Put(security: "is_granted('ROLE_ADMIN')", extraProperties: ["standard_put" => true]), // Remplacement réservé aux admins
        new Delete(security: "is_granted('ROLE_ADMIN')"), // Suppression réservée aux admins
        new Patch(security: "is_granted('ROLE_ADMIN')") // Mise à jour partielle réservée aux admins
    ]
)]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['user:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide.')]
    #[Assert\Length(max: 180)]
    private ?string $email = null;


    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Assert\Length(min: 2, minMessage: 'Le nom d\'utilisateur doit contenir au moins 4 caractères.')]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[Groups(['user:read'])]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Groups(['user:write'])]
    #[ORM\Column]
    #[Assert\NotBlank]
    // #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins 8 caractères.')]
    // #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/', message: 'Le mot de passe doit contenir au moins un caractère majuscule, un caractère minuscule et un chiffre.')]
    private ?string $password = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'loueur', cascade: ['remove'])]
    private Collection $reservations;

    /**
     * @var Collection<int, Pool>
     */
    #[ORM\OneToMany(targetEntity: Pool::class, mappedBy: 'owner', cascade: ['remove'], inversedBy: 'owner')]
    private Collection $pools;

    #[Groups(['user:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->pools = new ArrayCollection();
        $this->createdAt = new \DateTime();
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
        return $this->username;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
