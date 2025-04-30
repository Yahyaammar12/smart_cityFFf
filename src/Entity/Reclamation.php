<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\ReclamationRepository;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
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

    #[Assert\NotBlank(message:"Veuillez saisir le sujet.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $sujet = null;

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir la description.")]
    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reclamations')]
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

    #[Assert\NotBlank(message:"Veuillez saisir le rating.")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "Le rating doit être entre {{ min }} et {{ max }}.",
        invalidMessage: "Veuillez entrer un nombre valide."
    )]
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

    #[Assert\NotNull(message: "Veuillez indiquer si elle est traité ou non.")]
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $solved = null;

    public function isSolved(): ?bool
    {
        return $this->solved;
    }

    public function setSolved(?bool $solved): self
    {
        $this->solved = $solved;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir le tourisme.")]
    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $tourisme_id = null;

    public function getTourisme_id(): ?int
    {
        return $this->tourisme_id;
    }

    public function setTourisme_id(int $tourisme_id): self
    {
        $this->tourisme_id = $tourisme_id;
        return $this;
    }

    public function getTourismeId(): ?int
    {
        return $this->tourisme_id;
    }

    public function setTourismeId(int $tourisme_id): static
    {
        $this->tourisme_id = $tourisme_id;

        return $this;
    }

}
