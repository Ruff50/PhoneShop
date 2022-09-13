<?php

namespace App\Entity;

use App\Repository\CommandeDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeDetailsRepository::class)]
class CommandeDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commandeDetails')]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'commandeDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Column(length: 255)]
    private ?string $ProduitNom = null;

    #[ORM\Column]
    private ?float $ProduitPrix = null;

    #[ORM\Column]
    private ?float $ProduitTva = null;

    #[ORM\Column]
    private ?int $Qte = null;

    #[ORM\Column]
    private ?float $total = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getProduitNom(): ?string
    {
        return $this->ProduitNom;
    }

    public function setProduitNom(string $ProduitNom): self
    {
        $this->ProduitNom = $ProduitNom;

        return $this;
    }

    public function getProduitPrix(): ?float
    {
        return $this->ProduitPrix;
    }

    public function setProduitPrix(float $ProduitPrix): self
    {
        $this->ProduitPrix = $ProduitPrix;

        return $this;
    }

    public function getProduitTva(): ?float
    {
        return $this->ProduitTva;
    }

    public function setProduitTva(float $ProduitTva): self
    {
        $this->ProduitTva = $ProduitTva;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->Qte;
    }

    public function setQte(int $Qte): self
    {
        $this->Qte = $Qte;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }
}
