<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\DataTransformer;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transformation de la donnée de l'Objet Ingredient vers son nom.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * phpstan-ignore missingType.generics
 *
 * @implements DataTransformerInterface<Ingredient|null, string>
 */
class IngredientToNameTransformer implements DataTransformerInterface
{
    /**
     * Constructeur.
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Transforme l'objet (Ingredient) vers le nom de l'ingrédient.
     *
     * @psalm-param ?Ingredient $value
     */
    #[\Override]
    public function transform(mixed $value): string
    {
        if (!$value instanceof Ingredient) {
            return '';
        }

        return (string) $value->getName();
    }

    /**
     * Transforme le nom de l'ingredient en objet (Ingredient).
     *
     * @psalm-param ?string $value
     *
     * @throws TransformationFailedException si l'objet n'a pas été trouvé
     */
    #[\Override]
    public function reverseTransform(mixed $value): ?Ingredient
    {
        /**
         * Recherche l'ingrédient en fonction de son nom.
         */
        $ingredient = $this->entityManager
            ->getRepository(Ingredient::class)
            ->findOneBy(['name' => $value])
        ;

        // Si objet non trouvé
        if (null === $ingredient) {
            $privateErrorMessage = sprintf('An ingredient with name "%s" does not exist !', (string) $value);
            $publicErrorMessage = 'L\'ingrédient "{{ value }}" donné n\'existe pas encore.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $value,
            ]);

            throw $failure;
        }

        return $ingredient;
    }
}
