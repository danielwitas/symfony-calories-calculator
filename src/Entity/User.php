<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Yaml\Tests\A;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Template[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Template", mappedBy="owner")
     *
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $templates;

    /**
     * @var Meal[]|ArrayCollection
     *
     *@ORM\OneToMany(targetEntity="Meal", mappedBy="owner")
     *
     *@ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $meals;

    /**
     * @var Product[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="owner")
     *
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     *
     */
    private $products;

    public function __construct()
    {
        parent::__construct();

        $this->templates = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->meals = new ArrayCollection();
    }

    /**
     * @return Template[]|ArrayCollection
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param Template $template
     *
     * @return $this
     */
    public function addTemplate(Template $template)
    {
       $this->templates[] = $template;

       return $this;
    }

    /**
     * @return Product[]|ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $product
     *
     * @return $this
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * @return Meal[]|ArrayCollection
     */
    public function getMeals()
    {
        return $this->meals;
    }

    /**
     * @param Meal $meal
     *
     * @return $this
     */
    public function addMeal(Meal $meal)
    {
        $this->meals[] = $meal;

        return $this;
    }
}