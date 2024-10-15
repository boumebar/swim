<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\State\ReservationProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_USER')"),
        new Get(uriTemplate: '/reservations/{id}', security: "is_granted('ROLE_ADMIN') or object.getLoueur() == user", requirements: ['id' => '\d+'],),
        new Put(security: "is_granted('ROLE_ADMIN') or (object.getLoueur() == user and previous_object.getLoueur()
         == user)", extraProperties: ["standard_put" => true]),
        new Patch(security: "is_granted('ROLE_ADMIN') or object.getLoueur() == user"),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getLoueur() == user")
    ],
    processor: ReservationProcessor::class
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')] // Correct here
    #[ORM\JoinColumn(nullable: false)]
    private ?User $loueur = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pool $pool = null;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private ?bool $isApproved = false;

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

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;

        return $this;
    }
}
