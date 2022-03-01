<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $articlenumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $articlename;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?Order
    {
        return $this->parent;
    }

    public function setParent(?Order $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getArticlenumber(): ?string
    {
        return $this->articlenumber;
    }

    public function setArticlenumber(string $articlenumber): self
    {
        $this->articlenumber = $articlenumber;

        return $this;
    }

    public function getArticlename(): ?string
    {
        return $this->articlename;
    }

    public function setArticlename(string $articlename): self
    {
        $this->articlename = $articlename;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
