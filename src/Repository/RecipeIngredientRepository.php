<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Enum\Unity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecipeIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeIngredient[]    findAll()
 * @method RecipeIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<RecipeIngredient>
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
     * @return Unity[]
     */
    public function findMostPopularityUnityByIngredient(): array
    {
        /** @var RecipeIngredient[] $ingredients */
        $ingredients = $this->createQueryBuilder('ri')
            ->groupBy('ri.ingredient')
            ->addGroupBy('ri.unity')
            ->orderBy('COUNT(ri.unity)', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        /** @var Unity[] $result */
        $result = [];
        // Retourne le résultat par ID de l'ingredient
        foreach ($ingredients as $ingredient) {
            if (!$ingredient->getIngredient() instanceof Ingredient) {
                continue;
            }
            $result[$ingredient->getIngredient()->getId()] = $ingredient->getUnity();
        }

        return $result;
    }
}
