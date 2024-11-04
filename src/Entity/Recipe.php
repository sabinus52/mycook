<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Entity;

use App\Constant\Difficulty;
use App\Constant\Rate;
use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité d'une recette.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la recette.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * Nombre de personne pour les quantités définies dans la recette.
     */
    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 1, max: 12)]
    private ?int $person = 4;

    /**
     * Niveau de difficulté de la recette.
     */
    #[ORM\Column(type: 'difficulty')]
    #[Assert\NotNull]
    private ?Difficulty $difficulty = null;

    /**
     * Coût de la recette.
     */
    #[ORM\Column(type: 'rate')]
    #[Assert\NotNull]
    private ?Rate $rate = null;

    /**
     * Temps de préparation en minutes.
     */
    #[ORM\Column(name: 'time_preparation', type: Types::SMALLINT)]
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    private ?int $timePreparation = null;

    /**
     * Temps de cuisson en minutes.
     */
    #[ORM\Column(name: 'time_cooking', type: Types::SMALLINT, nullable: true)]
    #[Assert\Type(type: 'integer')]
    private ?int $timeCooking = null;

    /**
     * Nombre de calories de la recette.
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'integer')]
    private ?int $calorie = null;

    /**
     * Jointure avec les catégories.
     *
     * @var Collection|Category[]
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'recipes')]
    private Collection $categories;

    /**
     * Jointure avec les étapes.
     *
     * @var Collection|Step[]
     */
    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'recipe', orphanRemoval: true, cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $steps;

    /**
     * Jointure avec les ingrédients.
     *
     * @var Collection|RecipeIngredient[]
     */
    #[ORM\OneToMany(targetEntity: RecipeIngredient::class, mappedBy: 'recipe', orphanRemoval: true, cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $ingredients;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->steps = new ArrayCollection();
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

    public function getPerson(): ?int
    {
        return $this->person;
    }

    public function setPerson(int $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getDifficulty(): ?Difficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(?Difficulty $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getRate(): ?Rate
    {
        return $this->rate;
    }

    public function setRate(?Rate $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getTimePreparation(): ?int
    {
        return $this->timePreparation;
    }

    public function setTimePreparation(?int $timePreparation): static
    {
        $this->timePreparation = $timePreparation;

        return $this;
    }

    public function getTimeCooking(): ?int
    {
        return $this->timeCooking;
    }

    public function setTimeCooking(?int $timeCooking): static
    {
        $this->timeCooking = $timeCooking;

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
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|Step[]
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Step $step): static
    {
        if ($this->steps->removeElement($step) && $step->getRecipe() === $this) {
            $step->setRecipe(null);
        }

        return $this;
    }

    /**
     * @return Collection|RecipeIngredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(RecipeIngredient $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(RecipeIngredient $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient) && $ingredient->getRecipe() === $this) {
            $ingredient->setRecipe(null);
        }

        return $this;
    }

    /**
     * Calcul de nombre de calories de la recette par personne.
     */
    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function calculCalories(): ?int
    {
        $calories = 0;

        // Pour chaque ingrédient de la recette
        foreach ($this->ingredients as $ingredient) {
            $cal = $ingredient->getCalories();
            if (null === $cal) {
                // Manque les calories d'un élément donc impossible de calculer
                $this->calorie = null;

                return null;
            }
            $calories += $cal;
        }

        // Nombre de calories par personne
        $this->calorie = (int) round($calories / $this->person);

        return $this->calorie;
    }
}
