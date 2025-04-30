<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\LoisirRepository;

#[ORM\Entity(repositoryClass: LoisirRepository::class)]
#[ORM\Table(name: 'loisir')]
class Loisir
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

    #[Assert\NotBlank(message:"Veuillez saisir le nom.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez choisir le type.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir la localisation.")]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $localisation = null;

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir l horaire de travail.")]
    #[Assert\Regex(
        pattern: "/^(?:[01]\d|2[0-3]):[0-5]\d-(?:[01]\d|2[0-3]):[0-5]\d$/",
        message: "Le format de l'horaire de travail doit être sous la forme HH:MM-HH:MM (ex: 06:00-18:00)."
    )]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $horaire_travail = null;

    public function getHoraire_travail(): ?string
    {
        return $this->horaire_travail;
    }

    public function setHoraire_travail(?string $horaire_travail): self
    {
        $this->horaire_travail = $horaire_travail;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'loisirs')]
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

    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'loisir')]
    private Collection $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        if (!$this->evenements instanceof Collection) {
            $this->evenements = new ArrayCollection();
        }
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->getEvenements()->contains($evenement)) {
            $this->getEvenements()->add($evenement);
        }
        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        $this->getEvenements()->removeElement($evenement);
        return $this;
    }

    public function getHoraireTravail(): ?string
    {
        return $this->horaire_travail;
    }

    public function setHoraireTravail(?string $horaire_travail): static
    {
        $this->horaire_travail = $horaire_travail;

        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->id;
    }

}
