<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\RendezvouRepository;

#[ORM\Entity(repositoryClass: RendezvouRepository::class)]
#[ORM\Table(name: 'rendezvous')]
class Rendezvou
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

    #[Assert\NotBlank(message:"Veuillez choisir le statut.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'rendezvous')]
    #[ORM\JoinColumn(name: 'idPatient', referencedColumnName: 'id')]
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

    #[Assert\NotBlank(message:"Veuillez choisir le centre medicale.")]
    #[ORM\ManyToOne(targetEntity: Centremedicale::class, inversedBy: 'rendezvous')]
    #[ORM\JoinColumn(name: 'idCentreMedicale', referencedColumnName: 'id')]
    private ?Centremedicale $centremedicale = null;

    public function getCentremedicale(): ?Centremedicale
    {
        return $this->centremedicale;
    }

    public function setCentremedicale(?Centremedicale $centremedicale): self
    {
        $this->centremedicale = $centremedicale;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez choisir le nom du medecin.")]
    #[ORM\Column(name: 'nomMedecin', type: 'string', nullable: false)]
    private ?string $nomMedecin = null;

    public function getNomMedecin(): ?string
    {
        return $this->nomMedecin;
    }

    public function setNomMedecin(string $nomMedecin): self
    {
        $this->nomMedecin = $nomMedecin;
        return $this;
    }

}
