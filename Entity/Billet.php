<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\BilletRepository;

#[ORM\Entity(repositoryClass: BilletRepository::class)]
#[ORM\Table(name: 'billet')]
class Billet
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

    
    #[Assert\NotBlank(message: "Veuillez saisir la date.")]
    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_voyage = null;

    public function getDate_voyage(): ?\DateTimeInterface
    {
        return $this->date_voyage;
    }

    public function setDate_voyage(\DateTimeInterface $date_voyage): self
    {
        $this->date_voyage = $date_voyage;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?string $prix = null;

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez choisir le statut.")]
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

    #[Assert\NotBlank(message:"Veuillez choisir le payment status.")]
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $payment_status = null;

    public function getPayment_status(): ?string
    {
        return $this->payment_status;
    }

    public function setPayment_status(string $payment_status): self
    {
        $this->payment_status = $payment_status;
        return $this;
    }

    #[Assert\NotBlank(message:"Veuillez choisir le transport.")]
    #[ORM\ManyToOne(targetEntity: Transport::class, inversedBy: 'billets')]
    #[ORM\JoinColumn(name: 'transport_id', referencedColumnName: 'id')]
    private ?Transport $transport = null;

    public function getTransport(): ?Transport
    {
        return $this->transport;
    }

    public function setTransport(?Transport $transport): self
    {
        $this->transport = $transport;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'billets')]
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

    public function getDateVoyage(): ?\DateTimeInterface
    {
        return $this->date_voyage;
    }

    public function setDateVoyage(\DateTimeInterface $date_voyage): static
    {
        $this->date_voyage = $date_voyage;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(string $payment_status): static
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    


}
