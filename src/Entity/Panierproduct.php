<?php

namespace App\Entity;

use App\Repository\PanierproductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierproductRepository::class)]
class Panierproduct
{
    

    #[ORM\Column]
    private ?int $qt = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'panierproducts',cascade: ["persist"])]
    private ?Panier $idpanier = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'panierproducts')]
    private ?Product $idproduct = null;


   


    
    public function getQt(): ?int
    {
        return $this->qt;
    }

    public function setQt(int $qt): static
    {
        $this->qt = $qt;

        return $this;
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

    public function getIdpanier(): ?Panier
    {
        return $this->idpanier;
    }

    public function setIdpanier(?Panier $idpanier): static
    {
        $this->idpanier = $idpanier;

        return $this;
    }

    public function getIdproduct(): ?Product
    {
        return $this->idproduct;
    }

    public function setIdproduct(?Product $idproduct): static
    {
        $this->idproduct = $idproduct;

        return $this;
    }

   

   }
