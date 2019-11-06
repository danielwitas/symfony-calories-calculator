<?php


namespace App\customEvents;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;


class ProductEvent extends Event
{
    public const NAME ='product.opened';
 protected $product;
    public function __construct(Product $product)
 {
    $this->product = $product;
 }

    public function getProduct()
    {
        return $this->product;
 }
}