<?php
/**
 * Entité de jointure des ingrédients composant la recette
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity;

use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Constant\Unity;

/**
 * @ORM\Entity(repositoryClass=RecipeIngredientRepository::class)
 */
class RecipeIngredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Jointure avec les recettes
     * 
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="ingredients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * Jointure avec les ingédients
     * @ORM\ManyToOne(targetEntity=Ingredient::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ingredient;

    /**
     * Quantité de l'ingrédient de la recette
     * 
     * @var Integer
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $quantity;

    /**
     * Jointure avec l'unité de la quantité de l'ingrédient
     * 
     * @ORM\Column(type="unity")
     */
    private $unity;

    /**
     * Note supplémentaire
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

}
