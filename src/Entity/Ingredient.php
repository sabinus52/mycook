<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\IngredientRepository;
use App\ValuesList\Unity;
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
class Ingredient implements \Stringable
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
     * Unité par défaut.
     */
    #[ORM\Column(type: 'unity')]
    private Unity $unity;

    /**
     * Conversion: pour 1 unité combien cela représente en gramme.
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
     * @var Collection<int, RecipeIngredient>
     */
    #[ORM\OneToMany(targetEntity: RecipeIngredient::class, mappedBy: 'ingredient')]
    private Collection $recipes;

    public function __construct()
    {
        $this->unity = new Unity(Unity::NUMBER);
        $this->recipes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
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

    public function getUnity(): Unity
    {
        return $this->unity;
    }

    public function setUnity(Unity $unity): static
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
     * @return Collection<int,RecipeIngredient>
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
        if ($this->recipes->removeElement($recipe) && $recipe->getIngredient() === $this) {
            $recipe->setIngredient(null);
        }

        return $this;
    }
}
