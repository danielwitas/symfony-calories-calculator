<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Template;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAvarage($user, $limit = 7)
    {
        $templates = $this->getEntityManager()->getRepository(Template::class)->findBy(['owner' => $user], ['id' => 'DESC'], $limit);

        $totalCalories = null;
        $totalProtein = null;
        $totalCarbs = null;
        $totalFat = null;

        foreach ($templates as $template)
        {
            $products = $template->getProducts();

            foreach ($products as $product)
            {
                $totalCalories += $product->getTotalCalories();
                $totalProtein += $product->getTotalProtein();
                $totalCarbs += $product->getTotalCarbs();
                $totalFat += $product->getTotalFat();
            }

        }

        return [
            'calories' => round($totalCalories / $limit),
            'protein' => round($totalProtein / $limit),
            'carbs' => round($totalCarbs / $limit),
            'fat' => round($totalFat / $limit)
        ];

    }
}
