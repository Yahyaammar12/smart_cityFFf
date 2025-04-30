<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\DemandeserviceRepository;

#[ORM\Entity(repositoryClass: DemandeserviceRepository::class)]
#[ORM\Table(name: 'demandeservice')]
class Demandeservice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir la date.")]
    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir l utilisateur.")]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'demandeservices')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez choisir la service.")]
    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'demandeservices')]
    #[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'id')]
    private ?Service $service = null;

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir le statut.")]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    // #[Assert\NotBlank(message:"Veuillez saisir le rating.")]
    // #[Assert\Range(
    //     min: 1,
    //     max: 5,
    //     notInRangeMessage: "Le rating doit être entre {{ min }} et {{ max }}.",
    //     invalidMessage: "Veuillez entrer un nombre valide."
    // )]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rating = null;

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

}
