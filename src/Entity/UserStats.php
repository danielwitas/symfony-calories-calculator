<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserStatsRepository")
 */
class UserStats
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Calories;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Protein;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Carbs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Fat;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="stats")
     */
    private $owner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $productsAdded;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalories(): ?int
    {
        return $this->Calories;
    }

    public function setCalories(?int $Calories): self
    {
        $this->Calories = $Calories;

        return $this;
    }

    public function getProtein(): ?int
    {
        return $this->Protein;
    }

    public function setProtein(?int $Protein): self
    {
        $this->Protein = $Protein;

        return $this;
    }

    public function getCarbs(): ?int
    {
        return $this->Carbs;
    }

    public function setCarbs(?int $Carbs): self
    {
        $this->Carbs = $Carbs;

        return $this;
    }

    public function getFat(): ?int
    {
        return $this->Fat;
    }

    public function setFat(?int $Fat): self
    {
        $this->Fat = $Fat;

        return $this;
    }

    public function getProductsAdded(): ?int
    {
        return $this->productsAdded;
    }

    public function setProductsAdded(?int $productsAdded): self
    {
        $this->productsAdded = $productsAdded;

        return $this;
    }

    public function setOwner(User $user)
    {
        $this->owner = $user;

        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }
}
