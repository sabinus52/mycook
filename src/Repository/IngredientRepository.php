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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Ingredient>
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    /**
     * Recherche par nom de l'ingredient.
     *
     * @param string             $term : Valeur à rechercher
     * @param 1|2|3|4|5|6|string $mode : Hydratation mode de retour du résultat
     *
     * @return Ingredient[]
     */
    public function searchByName(string $term, $mode = Query::HYDRATE_OBJECT): array
    {
        // @phpstan-ignore return.type
        return $this->createQueryBuilder('i')
            ->where('i.name LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult($mode)
        ;
    }
}
