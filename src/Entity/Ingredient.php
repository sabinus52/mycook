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
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité des ingrédients.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[UniqueEntity('name')]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de l'ingrédient.
     */
    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * Unité par défaur.
     */
    #[ORM\Column(type: 'unity')]
    private ?Unity $unity = null;

    /**
     * Conversion.
     */
    #[ORM\Column(nullable: true)]
    private ?int $conversion = null;

    /**
     * Nombre de calorie pour 100 gramme.
     */
    #[ORM\Column(nullable: true)]
    private ?int $calorie = null;

    /**
     * Jointure avec les recettes.
     *
     * @var Collection|RecipeIngredient[]
     */
    #[ORM\OneToMany(targetEntity: RecipeIngredient::class, mappedBy: 'ingredient')]
    private Collection $recipes;

    public function __construct()
    {
        $this->unity = new Unity(Unity::NUMBER);
        $this->recipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUnity(): ?Unity
    {
        return $this->unity;
    }

    public function setUnity(?Unity $unity): static
    {
        $this->unity = $unity;

        return $this;
    }

    public function getConversion(): ?int
    {
        return $this->conversion;
    }

    public function setConversion(?int $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function getCalorie(): ?int
    {
        return $this->calorie;
    }

    public function setCalorie(?int $calorie): static
    {
        $this->calorie = $calorie;

        return $this;
    }

    /**
     * @return Collection|RecipeIngredient[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(RecipeIngredient $recipe): static
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setIngredient($this);
        }

        return $this;
    }

    public function removeRecipe(RecipeIngredient $recipe): static
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getIngredient() === $this) {
                $recipe->setIngredient(null);
            }
        }

        return $this;
    }

    /**
     * Retourne le poids en gramme de l'ingrédient à partir d'une certaine quantité.
     *
     * @param float $quantity : Quantity de l'ingrédient
     * @param Unity $source   : Unité de la quantité
     */
    public function getInGram(?float $quantity, Unity $source): ?float
    {
        if (!$source->isNumber()) {
            // Si pas un nombre, on peut convertir directement en gramme
            return $source->getInGram($quantity);
        }
        // Si un nombre, on vérifie si on peut convertir
        if (null === $this->conversion) {
            return null;
        }

        return round($this->conversion * $quantity);
    }

    /**
     * Retourne le nombre de calorie de l'ingrédient à partir d'une certaine quantité.
     *
     * @param float $quantity : Quantity de l'ingrédient
     * @param Unity $source   : Unité de la quantité
     */
    public function getCalories(?float $quantity, Unity $source): ?int
    {
        if (null === $this->calorie) {
            return null;
        }

        // Poids en gramme de l'ingrédient
        $mass = $this->getInGram($quantity, $source);
        if (null === $mass) {
            return null;
        }

        return (int) round($mass * $this->calorie / 100);
    }
}
