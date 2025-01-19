<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Recherche les recettes par catégorie.
     *
     * @return Recipe[]
     */
    public function findByCategory(Category $category): array
    {
        // @phpstan-ignore return.type
        return $this->createQueryBuilder('recipe')
            ->join('recipe.categories', 'category')
            ->andWhere('category.id = :id')
            ->setParameter('id', $category->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Recherche les recettes par ingrédient.
     *
     * @return Recipe[]
     */
    public function findByIngredient(Ingredient $ingredient): array
    {
        // @phpstan-ignore return.type
        return $this->createQueryBuilder('recipe')
            ->join('recipe.ingredients', 'ri')
            ->andWhere('ri.ingredient = :id')
            ->setParameter('id', $ingredient->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retourne les recettes les plus populaires.
     *
     * @return Recipe[]
     */
    public function findMostPopular(int $count = 6): array
    {
        // @phpstan-ignore return.type
        return $this->createQueryBuilder('recipe')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retourne une recette au hasard.
     */
    public function findOneRandom(): Recipe
    {
        /** @phpstan-ignore return.type */
        return $this->createQueryBuilder('recipe')
            ->addSelect('RANDOM() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }
}
