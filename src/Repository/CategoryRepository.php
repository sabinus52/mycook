<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }


    /**
     * Retourne les catégories où il y a le plus de recettes
     * 
     * @param Integer $count : Nombre d'occ à retourner
     */
    public function findMostRecipes(?int $count = null): array
    {
        $query = $this->createQueryBuilder('cat')
            ->addSelect('COUNT(recipe) AS nb')
            ->join('cat.recipes', 'recipe')
            ->groupBy('cat.id')
            ->orderBy('nb', 'DESC');

        if ( $count ) $query = $query->setMaxResults(6);
        
        return $query->getQuery()->getResult();
    }

}