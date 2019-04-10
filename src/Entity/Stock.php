<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockRepository")
 */
class Stock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Store", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $store;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbProduct(): ?int
    {
        return $this->nb_product;
    }

    public function setNbProduct(int $nb_product): self
    {
        $this->nb_product = $nb_product;

        return $this;
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

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): self
    {
        $this->store = $store;

        return $this;
    }
}
