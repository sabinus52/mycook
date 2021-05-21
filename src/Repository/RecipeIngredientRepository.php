<?php

namespace App\Repository;

use App\Entity\RecipeIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecipeIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeIngredient[]    findAll()
 * @method RecipeIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeIngredientRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeIngredient::class);
    }


    /**
     * Retourne dans un tableau les unités les plus utilisées par ingredient
     * 
     * @return Array [ID_ingredient] => Unity
     */
    public function findMostPopularityUnityByIngredient()
    {
        $query = $this->createQueryBuilder('ri')
            ->addSelect('COUNT(ri.unity)')
            ->groupBy('ri.ingredient')
            ->addGroupBy('ri.unity')
            ->orderBy('COUNT(ri.unity)', 'ASC')
            ->getQuery()
        ;

        $result = [];
        // Retourne le resultat par ID de l'ingredient
        foreach ($query->getResult() as $ingredient) {
            $result[$ingredient[0]->getIngredient()->getId()] = $ingredient[0]->getUnity();
        }

        return $result;
    }

}