<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Recipe;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

/**
 * Autocompleter pour les recettes.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 * @template-implements EntityAutocompleterInterface<Recipe>
 */
#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'recipe'])]
final class RecipeAutoCompleter implements EntityAutocompleterInterface
{
    public function __construct(protected RouterInterface $router)
    {
    }

    #[\Override]
    public function getEntityClass(): string
    {
        return Recipe::class;
    }

    /**
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    #[\Override]
    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository
            ->createQueryBuilder('recipe')
            ->andWhere('recipe.name LIKE :search')
            ->setParameter('search', '%'.$query.'%')
        ;
    }

    /**
     * @param Recipe $entity
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    #[\Override]
    public function getLabel(object $entity): string
    {
        return (string) $entity->getName();
    }

    /**
     * @param Recipe $entity
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    #[\Override]
    public function getValue(object $entity): string
    {
        return $this->router->generate('recipe_show', ['id' => $entity->getId()]);
    }

    #[\Override]
    public function isGranted(Security $security): bool
    {
        // see the "security" option for details
        return true;
    }
}
