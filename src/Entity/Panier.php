<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $total = null;


    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\OneToMany(targetEntity: Panierproduct::class, mappedBy: 'idpanier')]
    private Collection $panierproducts;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function __construct()
    {
        $this->panierproducts = new ArrayCollection();
    }


   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }


 
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Panierproduct>
     */
    public function getPanierproducts(): Collection
    {
        return $this->panierproducts;
    }

    public function addPanierproduct(Panierproduct $panierproduct): static
    {
        if (!$this->panierproducts->contains($panierproduct)) {
            $this->panierproducts->add($panierproduct);
            $panierproduct->setIdpanier($this);
        }

        return $this;
    }

    public function removePanierproduct(Panierproduct $panierproduct): static
    {
        if ($this->panierproducts->removeElement($panierproduct)) {
            // set the owning side to null (unless already changed)
            if ($panierproduct->getIdpanier() === $this) {
                $panierproduct->setIdpanier(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }




    
}
