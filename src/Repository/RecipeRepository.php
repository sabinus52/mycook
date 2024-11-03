<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Retourne la recette avec tous ses ingrédients.
     *
     * @param int $id : Identifiant de la recette
     */
    public function findWithIngredients($id): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->addSelect('ri')
            ->addSelect('i')
            ->leftJoin('r.ingredients', 'ri')
            ->leftJoin('ri.ingredient', 'i')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Recherche les recettes par catégorie.
     *
     * @param Category $categorie : Catégorie à filtrer
     *
     * @return Recipe[]
     */
    public function findByCategory(Category $categorie): array
    {
        return $this->createQueryBuilder('recipe')
            ->join('recipe.categories', 'category')
            ->andWhere('category.id = :id')
            ->setParameter('id', $categorie->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retourne les recettes les plus populaires.
     *
     * @param int $count : Nombre d'occ à retourner
     *
     * @return Recipe[]
     */
    public function findMostPopular(?int $count = null): array
    {
        $query = $this->createQueryBuilder('recipe');

        if ($count) {
            $query = $query->setMaxResults(6);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Retourne une recette au hasard.
     *
     * @return Recipe
     */
    public function findOneRandom(): Recipe
    {
        $query = $this->createQueryBuilder('recipe')
            ->addSelect('RANDOM() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }
}
