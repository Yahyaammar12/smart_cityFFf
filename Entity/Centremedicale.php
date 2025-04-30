<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\CentremedicaleRepository;

#[ORM\Entity(repositoryClass: CentremedicaleRepository::class)]
#[ORM\Table(name: 'centremedicale')]
class Centremedicale
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

    #[Assert\NotNull(message: "Veuillez indiquer la disponibilité.")]
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $disponibilite = null;

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?bool $disponibilite): self
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Rendezvou::class, mappedBy: 'centremedicale')]
    private Collection $rendezvous;

    public function __construct()
    {
        $this->rendezvous = new ArrayCollection();
    }

    /**
     * @return Collection<int, Rendezvou>
     */
    public function getRendezvous(): Collection
    {
        if (!$this->rendezvous instanceof Collection) {
            $this->rendezvous = new ArrayCollection();
        }
        return $this->rendezvous;
    }

    public function addRendezvou(Rendezvou $rendezvou): self
    {
        if (!$this->getRendezvous()->contains($rendezvou)) {
            $this->getRendezvous()->add($rendezvou);
        }
        return $this;
    }

    public function removeRendezvou(Rendezvou $rendezvou): self
    {
        $this->getRendezvous()->removeElement($rendezvou);
        return $this;
    }


    public function __toString(): string
    {
        return (string)$this->id;
    }

}
