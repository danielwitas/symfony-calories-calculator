<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    const STATUS_SHARED = "shared";
    const STATUS_PRIVATE = "private";
    const STATUS_PUBLIC = "public";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @Assert\Length(
     *     min=3,
     *     max=25
     *     )
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="decimal", precision=10, scale=1)
     *
     * @Assert\NotBlank()
     *
     * @Assert\GreaterThan(
     *  value="0"
     * )
     *
     *
     */
    private $value;

    /**
     * @var float
     *
     * @ORM\Column(name="calories", type="decimal", precision=10, scale=1)
     *
     * @Assert\NotBlank()
     *
     * @Assert\GreaterThan(
     *  value="0"
     * )
     *
     *
     */
    private $calories;

    /**
     * @var float
     *
     * @ORM\Column(name="protein", type="decimal", precision=10, scale=1)
     *
     * @Assert\NotBlank()
     *
     * @Assert\GreaterThan(
     *  value="0"
     * )
     *
     *
     */
    private $protein;

    /**
     * @var float
     *
     * @ORM\Column(name="carbs", type="decimal", precision=10, scale=1)
     *
     * @Assert\NotBlank()
     *
     * @Assert\GreaterThan(
     *  value="0"
     * )
     *
     *
     */
    private $carbs;

    /**
     * @var float
     *
     * @ORM\Column(name="fat", type="decimal", precision=10, scale=1)
     *
     * @Assert\NotBlank()
     *
     * @Assert\GreaterThan(
     *  value="0"
     * )
     *
     *
     */
    private $fat;

    /**
     * @var float
     *
     * @ORM\Column(name="total_calories", nullable=true, type="decimal", precision=10, scale=1)
     */
    private $totalCalories;


    /**
     * @var float
     *
     * @ORM\Column(name="total_protein", nullable=true, type="decimal", precision=10, scale=1)
     */
    private $totalProtein;

    /**
     * @var float
     *
     * @ORM\Column(name="total_carbs", nullable=true, type="decimal", precision=10, scale=1)
     */
    private $totalCarbs;

    /**
     * @var float
     *
     * @ORM\Column(name="total_fat", nullable=true, type="decimal", precision=10, scale=1)
     */
    private $totalFat;


    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="products")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

    /**
     * @var Meal
     *
     * @ORM\ManyToOne(targetEntity="Meal", inversedBy="products")
     * @ORM\JoinColumn(name="meal_id", referencedColumnName="id")
     */
    private $meal;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="products")
     *
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var string

     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCalories(): ?float
    {
        return $this->calories;
    }

    public function setCalories(float $calories): self
    {
        $this->calories = $calories;

        return $this;
    }

    public function getProtein(): ?float
    {
        return $this->protein;
    }

    public function setProtein(float $protein): self
    {
        $this->protein = $protein;

        return $this;
    }

    public function getCarbs(): ?float
    {
        return $this->carbs;
    }

    public function setCarbs(float $carbs): self
    {
        $this->carbs = $carbs;

        return $this;
    }

    public function getFat(): ?float
    {
        return $this->fat;
    }

    public function setFat(float $fat): self
    {
        $this->fat = $fat;

        return $this;
    }


    /**
     * @param Template $template
     *
     * @return $this
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Meal|null $meal
     *
     * @return $this
     */
    public function setMeal(Meal $meal = null)
    {
        $this->meal = $meal;

        return $this;
    }

    /**
     * @return Meal
     */
    public function getMeal()
    {
        return $this->meal;
    }

    /**
     * @param User $owner
     *
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param $ingredient
     *
     * @return float|int
     */
    public function countTotal($ingredient)
    {
        $ingredient = ($ingredient * $this->value) / 100;

        return $ingredient;
    }

    /**
     * @param $totalCalories
     *
     * @return $this
     */
    public function setTotals()
    {
        $this->totalCalories = $this->countTotal($this->calories);
        $this->totalProtein = $this->countTotal($this->protein);
        $this->totalCarbs = $this->countTotal($this->carbs);
        $this->totalFat = $this->countTotal($this->fat);

        return $this;
    }

    /**
     * @param $totalCalories
     *
     * @return $this
     */
    public function setTotalCalories($totalCalories)
    {
        $this->totalCalories = $totalCalories;
        
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalCalories()
    {
        return $this->totalCalories;
    }

    /**
     * @param $totalProtein
     *
     * @return $this
     */
    public function setTotalProtein($totalProtein)
    {
        $this->totalProtein = $totalProtein;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalProtein()
    {
        return $this->totalProtein;
    }

    /**
     * @param $totalCarbs
     *
     * @return $this
     */
    public function setTotalCarbs($totalCarbs)
    {
        $this->totalCarbs = $totalCarbs;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalCarbs()
    {
        return $this->totalCarbs;
    }

    /**
     * @param $totalFat
     *
     * @return $this
     */
    public function setTotalFat($totalFat)
    {
        $this->totalFat = $totalFat;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalFat()
    {
        return $this->totalFat;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

}
