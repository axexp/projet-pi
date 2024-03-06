<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\OneToMany(targetEntity: Panierproduct::class, mappedBy: 'idproduct')]
    private Collection $panierproducts;

    public function __construct()
    {
        $this->panierproducts = new ArrayCollection();
    }

    


   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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
            $panierproduct->setIdproduct($this);
        }

        return $this;
    }

    public function removePanierproduct(Panierproduct $panierproduct): static
    {
        if ($this->panierproducts->removeElement($panierproduct)) {
            // set the owning side to null (unless already changed)
            if ($panierproduct->getIdproduct() === $this) {
                $panierproduct->setIdproduct(null);
            }
        }

        return $this;
    }

    

}
