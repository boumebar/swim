<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/reservations', security: "is_granted('ROLE_USER')"), // Lecture réservée aux utilisateurs
        new Post(security: "is_granted('ROLE_USER')"), // Création réservée aux utilisateurs
        new Get(uriTemplate: '/reservations/{id}', security: "is_granted('ROLE_USER')"), // Lecture d'une réservation réservée aux utilisateurs
        new Put(security: "is_granted('ROLE_ADMIN')"), // Remplacement réservé aux admins
        new Delete(security: "is_granted('ROLE_ADMIN')"), // Suppression réservée aux admins
        new Patch(security: "is_granted('ROLE_ADMIN')") // Mise à jour partielle réservée aux admins
    ]
)]

class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'pool')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $loueur = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pool $pool = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLoueur(): ?User
    {
        return $this->loueur;
    }

    public function setLoueur(?User $loueur): static
    {
        $this->loueur = $loueur;

        return $this;
    }

    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): static
    {
        $this->pool = $pool;

        return $this;
    }
}
