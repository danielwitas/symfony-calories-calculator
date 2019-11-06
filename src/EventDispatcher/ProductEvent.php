<?php


namespace App\EventDispatcher;


use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductEvent extends Event
{
    /**
     * @var Product
     */
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}