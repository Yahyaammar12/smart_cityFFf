<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\TransportRepository;

#[ORM\Entity(repositoryClass: TransportRepository::class)]
#[ORM\Table(name: 'transport')]
class Transport
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

    #[Assert\NotBlank(message:"Veuillez choisir une image.")]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    #[Assert\NotNull(message: "Veuillez saisir l'horaire.")]
    #[ORM\Column(type: 'time', nullable: false)]
    private ?\DateTimeInterface $horaire = null;

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(\DateTimeInterface $horaire): self
    {
        $this->horaire = $horaire;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir la tarif.")]
    #[Assert\Type(
        type: 'numeric',
        message: "Le tarif doit être un nombre décimal valide."
    )]
    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?string $tarif = null;

    public function getTarif(): ?string
    {
        return $this->tarif;
    }

    public function setTarif(string $tarif): self
    {
        $this->tarif = $tarif;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir les places libres.")]
    #[Assert\Range(
        min: 0,
        minMessage: "Le nombre de places libres ne peut pas être négatif."
    )]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $places_libres = null;

    public function getPlaces_libres(): ?int
    {
        return $this->places_libres;
    }

    public function setPlaces_libres(?int $places_libres): self
    {
        $this->places_libres = $places_libres;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir le point depart.")]
    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $depart = null;

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): self
    {
        $this->depart = $depart;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir le point destination.")]
    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $destination = null;

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir company.")]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $company = null;

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Billet::class, mappedBy: 'transport')]
    private Collection $billets;

    public function __construct()
    {
        $this->billets = new ArrayCollection();
    }

    /**
     * @return Collection<int, Billet>
     */
    public function getBillets(): Collection
    {
        if (!$this->billets instanceof Collection) {
            $this->billets = new ArrayCollection();
        }
        return $this->billets;
    }

    public function addBillet(Billet $billet): self
    {
        if (!$this->getBillets()->contains($billet)) {
            $this->getBillets()->add($billet);
        }
        return $this;
    }

    public function removeBillet(Billet $billet): self
    {
        $this->getBillets()->removeElement($billet);
        return $this;
    }

    public function getPlacesLibres(): ?int
    {
        return $this->places_libres;
    }

    public function setPlacesLibres(?int $places_libres): static
    {
        $this->places_libres = $places_libres;

        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->id;
    }

}
