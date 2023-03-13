<?php

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
// use Doctrine\Common\Collections\Collection;
use App\Entity\Nutrition;

/**
 * @ORM\Entity(repositoryClass=FruitRepository::class)
 */
class Fruit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genus;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $family;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fruitOrder;

    /**
     * @ORM\OneToOne(targetEntity="Nutrition", inversedBy="fruit")
     * @ORM\JoinColumn(name="nutrition_id", referencedColumnName="id")
     */
    private $nutrition;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(string $genus): self
    {
        $this->genus = $genus;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getOrder(): ?string
    {
        return $this->fruitOrder;
    }

    public function setOrder(string $fruitOrder): self
    {
        $this->fruitOrder = $fruitOrder;

        return $this;
    }

    /**
     * Get the value of nutrition
     */ 
    public function getNutrition()
    {
        return $this->nutrition;
    }

    /**
     * Set the value of nutrition
     *
     * @return  self
     */ 
    public function setNutrition($nutrition)
    {
        $this->nutrition = $nutrition;

        return $this;
    }
}
