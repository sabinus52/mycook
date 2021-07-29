<?php
/**
 * Transformation de la donnée de l'Objet Ingredient vers son nom
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Form\DataTransformer;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class IngredientToNameTransformer implements DataTransformerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * Constructeur
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Transforme l'objet (Ingredient) vers le nom de l'ingrédient
     *
     * @param  Ingredient|null $ingredient
     */
    public function transform($ingredient): string
    {
        if (null === $ingredient) {
            return '';
        }

        return $ingredient->getName();
    }


    /**
     * Transforme le nom de l'ingredient en objet (Ingredient)
     *
     * @param String $name
     * @throws TransformationFailedException si l'objet n'a pas été trouvé
     */
    public function reverseTransform($name): ?Ingredient
    {
        // Recherche l'ingrédient en fonction de son nom
        $ingredient = $this->entityManager
            ->getRepository(Ingredient::class)
            ->findOneByName($name)
        ;

        // Si objet non trouvé
        if (null === $ingredient) {
            $privateErrorMessage = sprintf('An ingredient with name "%s" does not exist !', $name);
            $publicErrorMessage = 'L\'ingrédient "{{ value }}" donné n\'existe pas encore.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $name,
            ]);

            throw $failure;
        }

        return $ingredient;
    }

}