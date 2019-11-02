<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TemplateRepository")
 */
class Template
{
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
     * @Assert\Length(
     *     min=3,
     *     max=255
     *     )
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;



    /**
     * @var Product[]
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="template", cascade="remove")
     */
    private $products;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="templates")
     */
    private $owner;

    /**
     * @var float
     *
     * @ORM\Column(name="totalCalories", nullable=true, type="decimal", precision=10, scale=2)

     */
    private $totalCalories = null;


    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Product[]|ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product $product
     *
     * @return $this
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Template
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param User $owner
     *
     * @return $this
     */
    public function setOwner(User $owner)
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

    public function setTotalCalories()
    {
        $this->totalCalories = null;
        foreach ($this->products as $product) {
          $this->totalCalories += $product->getCalories();
      }

        return $this;
    }

    public function getTotalCalories()
    {
        foreach ($this->products as $product) {
            $this->totalCalories += $product->getCalories();
        }

        return $this->totalCalories;
    }
}
