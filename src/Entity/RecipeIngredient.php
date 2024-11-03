<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Entity;

use App\Constant\Unity;
use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité de jointure des ingrédients composant la recette.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @ORM\Entity(repositoryClass=RecipeIngredientRepository::class)
 */
class RecipeIngredient
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id; /** @phpstan-ignore-line */

    /**
     * Jointure avec les recettes.
     *
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="ingredients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * Jointure avec les ingédients.
     *
     * @var Ingredient
     *
     * @ORM\ManyToOne(targetEntity=Ingredient::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $ingredient;

    /**
     * Quantité de l'ingrédient de la recette.
     *
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $quantity;

    /**
     * Jointure avec l'unité de la quantité de l'ingrédient.
     *
     * @var Unity
     *
     * @ORM\Column(type="unity")
     */
    private $unity;

    /**
     * Note supplémentaire.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnity(): ?Unity
    {
        return $this->unity;
    }

    public function setUnity(?Unity $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Retourne le nombre de calories de l'ingrédient.
     */
    public function getCalories(): ?int
    {
        return $this->getIngredient()->getCalories($this->quantity, $this->unity);
    }
}
