<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Enum\Unity;
use App\Repository\RecipeIngredientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité de jointure des ingrédients composant la recette.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
#[ORM\Entity(repositoryClass: RecipeIngredientRepository::class)]
class RecipeIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Jointure avec les recettes.
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'ingredients')]
    private ?Recipe $recipe = null;

    /**
     * Jointure avec les ingrédients.
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'recipes')]
    #[Assert\NotBlank]
    private ?Ingredient $ingredient = null;

    /**
     * Quantité de l'ingrédient de la recette.
     */
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $quantity = null;

    /**
     * Jointure avec l'unité de la quantité de l'ingrédient.
     */
    #[ORM\Column(enumType: Unity::class, length: 2)]
    private Unity $unity = Unity::GRAM;

    /**
     * Note supplémentaire.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getIngredient2(): Ingredient
    {
        if (!$this->ingredient instanceof Ingredient) {
            throw new \Exception('La recette n\'a pas d\'ingrédient');
        }

        return $this->ingredient;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnity(): Unity
    {
        return $this->unity;
    }

    public function setUnity(Unity $unity): static
    {
        $this->unity = $unity;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Retourne le nombre de calories de l'ingrédient inclus dans le recette.
     */
    public function getCalories(): ?int
    {
        // Si on peut pas calculer le poids total de l'ingrédient dans le recette, on ne peut pas calculer
        $weight = $this->getWeightInGram();
        if (null === $weight) {
            return null;
        }

        // Si l'ingrédient n'a pas de calorie, on ne peut pas calculer
        if (null === $this->getIngredient2()->getCalorie()) {
            return null;
        }

        /** @psalm-suppress PossiblyNullOperand */
        return (int) round($this->getIngredient2()->getCalorie() * $weight / 100);
    }

    /**
     * Retourne le poids en gramme la quantité de l'ingrédient inclus dans le recette.
     */
    public function getWeightInGram(): ?int
    {
        if (!$this->unity->isNumber()) {
            // Si l'unité choisie n'est pas un nombre, on peut convertir directement en gramme
            return (int) round($this->unity->inGram((float) $this->quantity));
        }

        // Si un nombre, on vérifie si on peut convertir
        if (null === $this->getIngredient2()->getConversion()) {
            return null;
        }

        /** @psalm-suppress PossiblyNullOperand */
        return (int) round($this->getIngredient2()->getConversion() * (int) $this->quantity);
    }
}
