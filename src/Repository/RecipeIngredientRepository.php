<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Ingredient;
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
     * Retourne dans un tableau les unités les plus utilisées par ingredient.
     *
     * @return array<Ingredient>
     */
    public function findMostPopularityUnityByIngredient(): array
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
