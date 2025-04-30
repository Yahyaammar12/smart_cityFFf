<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\ServiceRepository;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: 'service')]
class Service
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

    #[Assert\NotBlank(message:"Veuillez saisir le type.")]
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

    #[Assert\NotBlank(message:"Veuillez saisir l image.")]
    #[ORM\Column(name: 'imagePath',type: 'string', nullable: true)]
    private ?string $imagePath = null;

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Demandeservice::class, mappedBy: 'service')]
    private Collection $demandeservices;

    public function __construct()
    {
        $this->demandeservices = new ArrayCollection();
    }

    /**
     * @return Collection<int, Demandeservice>
     */
    public function getDemandeservices(): Collection
    {
        if (!$this->demandeservices instanceof Collection) {
            $this->demandeservices = new ArrayCollection();
        }
        return $this->demandeservices;
    }

    public function addDemandeservice(Demandeservice $demandeservice): self
    {
        if (!$this->getDemandeservices()->contains($demandeservice)) {
            $this->getDemandeservices()->add($demandeservice);
        }
        return $this;
    }

    public function removeDemandeservice(Demandeservice $demandeservice): self
    {
        $this->getDemandeservices()->removeElement($demandeservice);
        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->id;
    }

}
