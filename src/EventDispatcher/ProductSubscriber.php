<?php


namespace App\EventDispatcher;


use App\Entity\News;
use App\Entity\UserStats;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PRODUCT_SHARE => 'createNews'
        ];
    }

    public function createNews(ProductEvent $event)
    {
        $product = $event->getProduct();
        
        $news = new News();
        $news
            ->setTitle("{$product->getOwner()} added new product to public database!")
            ->setDescription("Name {$product->getName()}, Calories: {$product->getCalories()}, Protein: {$product->getProtein()}, Carbs: {$product->getCarbs()}, Fat: {$product->getFat()}.");

        $this->entityManager->persist($news);

        $userStats = $this->entityManager->getRepository(UserStats::class)->findOneBy(['owner' => $product->getOwner()]);
        $userStats->setOwner($product->getOwner());
        $userStats->setProductsAdded($userStats->getProductsAdded() + 1);
        $this->entityManager->persist($userStats);

        $this->entityManager->flush();


    }
}