<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\TourismeRepository;

#[ORM\Entity(repositoryClass: TourismeRepository::class)]
#[ORM\Table(name: 'tourisme')]
class Tourisme
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

    #[Assert\NotBlank(message:"Veuillez saisir le nombre de etoiles.")]
    #[Assert\Range(
        notInRangeMessage: "Le nombre d'étoiles doit être entre {{ min }} et {{ max }}.",
        min: 1,
        max: 5
    )]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nb_etoiles = null;

    public function getNb_etoiles(): ?int
    {
        return $this->nb_etoiles;
    }

    public function setNb_etoiles(?int $nb_etoiles): self
    {
        $this->nb_etoiles = $nb_etoiles;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir le contact.")]
    #[Assert\Regex(
        pattern: "/^[234579]\d{1} ?\d{3} ?\d{3}$/",
        message: "Le contact doit être un numéro tunisien valide à 8 chiffres (ex : 20 000 000)."
    )]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $contact = null;

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez saisir le price range.")]
    #[Assert\Regex(
        pattern: "/^\d{1,5}-\d{1,5}$/",
        message: "Le format du price range doit être comme ceci : 200-250."
    )]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $price_range = null;

    public function getPrice_range(): ?string
    {
        return $this->price_range;
    }

    public function setPrice_range(?string $price_range): self
    {
        $this->price_range = $price_range;
        return $this;
    }

    public function getNbEtoiles(): ?int
    {
        return $this->nb_etoiles;
    }

    public function setNbEtoiles(?int $nb_etoiles): static
    {
        $this->nb_etoiles = $nb_etoiles;

        return $this;
    }

    public function getPriceRange(): ?string
    {
        return $this->price_range;
    }

    public function setPriceRange(?string $price_range): static
    {
        $this->price_range = $price_range;

        return $this;
    }

}
