<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\EvenementRepository;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ORM\Table(name: 'evenement')]
class Evenement
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

    #[Assert\NotBlank(message:"Veuillez saisir la description.")]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    #[Assert\NotBlank(message:"Veuillez saisir l heure.")]
    #[ORM\Column(type: 'time', nullable: false)]
    private ?\DateTimeInterface $heure = null;

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): self
    {
        $this->heure = $heure;
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


    #[Assert\NotBlank(message:"Veuillez choisir le Moyen de Loisir.")]
    #[ORM\ManyToOne(targetEntity: Loisir::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(name: 'loisir_id', referencedColumnName: 'id')]
    private ?Loisir $loisir = null;

    public function getLoisir(): ?Loisir
    {
        return $this->loisir;
    }

    public function setLoisir(?Loisir $loisir): self
    {
        $this->loisir = $loisir;
        return $this;
    }

}
