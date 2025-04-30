<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface

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

    #[Assert\NotBlank(message: "Veuillez saisir le nom.")]
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

    #[Assert\NotBlank(message: "Veuillez saisir le prenom.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    #[Assert\NotBlank(message: "Veuillez saisir l email.")]
    #[Assert\Email(
        message: "L'adresse email '{{ value }}' n'est pas valide.",
        mode: 'html5' // or 'strict' if you want to be more restrictive
    )]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[Assert\NotBlank(message: "Veuillez saisir le numero de carte.")]
    #[Assert\Length(
        exactMessage: "Le numéro de carte doit contenir exactement 16 chiffres.",
        min: 16,
        max: 16
    )]
    #[Assert\Regex(
        pattern: "/^\d{16}$/",
        message: "Le numéro de carte doit contenir uniquement des chiffres."
    )]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $num_carte = null;

    public function getNumCarte(): ?string
    {
        return $this->num_carte;
    }

    public function setNumCarte(?string $num_carte): self
    {
        $this->num_carte = $num_carte;
        return $this;
    }

    #[Assert\NotBlank(message: "Veuillez saisir l adresse.")]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adresse = null;

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    #[Assert\NotBlank(message: "Veuillez saisir le mot de passe.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $motDePasse = null;

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }


    #[Assert\NotBlank(message: "Veuillez choisir le role.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $role = null;

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $is_verified = false;

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsverified(?bool $is_verified): self
    {
        $this->is_verified = $is_verified;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $actif = null;

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $created_at = null;

    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedat(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Billet::class, mappedBy: 'user')]
    private Collection $billets;

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

    #[ORM\OneToMany(targetEntity: Demandeservice::class, mappedBy: 'user')]
    private Collection $demandeservices;

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

    #[ORM\OneToMany(targetEntity: Loisir::class, mappedBy: 'user')]
    private Collection $loisirs;

    /**
     * @return Collection<int, Loisir>
     */
    public function getLoisirs(): Collection
    {
        if (!$this->loisirs instanceof Collection) {
            $this->loisirs = new ArrayCollection();
        }
        return $this->loisirs;
    }

    public function addLoisir(Loisir $loisir): self
    {
        if (!$this->getLoisirs()->contains($loisir)) {
            $this->getLoisirs()->add($loisir);
        }
        return $this;
    }

    public function removeLoisir(Loisir $loisir): self
    {
        $this->getLoisirs()->removeElement($loisir);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'user')]
    private Collection $reclamations;

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        if (!$this->reclamations instanceof Collection) {
            $this->reclamations = new ArrayCollection();
        }
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->getReclamations()->contains($reclamation)) {
            $this->getReclamations()->add($reclamation);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        $this->getReclamations()->removeElement($reclamation);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Rendezvou::class, mappedBy: 'user')]
    private Collection $rendezvous;

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
    
    public function getPassword(): ?string
    {
        return $this->motDePasse;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        // Using the built-in password_verify function to check the password
        return password_verify($plainPassword, $this->motDePasse);
    }

    public function getRoles(): array
    {
        return [$this->role]; // ou ['ROLE_USER'] si tu préfères par défaut
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Vider les données sensibles si tu en as, par exemple un champ de mot de passe temporaire
    }
    public function getUsername(): string
    {
        return $this->email; // Ou return $this->getEmail();
    }

    public function getSalt(): ?string
    {
        // Pas nécessaire si tu utilises bcrypt ou sodium (algorithmes modernes)
        return null;
    }

    public function __toString(): string
    {
        return (string)$this->id;
    }

}
