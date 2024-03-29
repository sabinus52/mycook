<?php
/**
 * Entité d'une recette
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constant\Difficulty;
use App\Constant\Rate;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Nom de la recette
     * 
     * @var String
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * Nombre de personne pour les quantités définies dans la recette
     * 
     * @var Integer
     * @ORM\Column(type="smallint")
     * @Assert\NotNull
     * @Assert\Type(type="integer")
     * @Assert\Range(min = 1, max = 12)
     */
    private $person;

    /**
     * Niveau de difficulté de la recette
     * 
     * @var Integer
     * @ORM\Column(type="difficulty")
     * @Assert\NotNull
     */
    private $difficulty;

    /**
     * Coût de la recette
     * 
     * @var Integer
     * @ORM\Column(type="rate")
     * @Assert\NotNull
     */
    private $rate;

    /**
     * Temps de préparation en minutes
     * 
     * @var Integer
     * @ORM\Column(name="time_preparation", type="smallint")
     * @Assert\NotNull
     * @Assert\Type(type="integer")
     */
    private $timePreparation;

    /**
     * Temps de cuisson en minutes
     * 
     * @var Integer
     * @ORM\Column(name="time_cooking", type="smallint", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $timeCooking;

    /**
     * Nombre de calories de la recette
     * 
     * @var Integer
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $calorie;

    /**
     * Jointure avec les catégories
     * 
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="recipes")
     */
    private $categories;

    /**
     * Jointure avec les étapes
     * 
     * @ORM\OneToMany(targetEntity=Step::class, mappedBy="recipe", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $steps;

    /**
     * Jointure avec les ingrédients
     * 
     * @ORM\OneToMany(targetEntity=RecipeIngredient::class, mappedBy="recipe", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $ingredients;


    public function __construct()
    {
        $this->person = 4;
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPerson(): ?int
    {
        return $this->person;
    }

    public function setPerson(int $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getDifficulty(): ?Difficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(?Difficulty $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getRate(): ?Rate
    {
        return $this->rate;
    }

    public function setRate(?Rate $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getTimePreparation(): ?int
    {
        return $this->timePreparation;
    }

    public function setTimePreparation(?int $timePreparation): self
    {
        $this->timePreparation = $timePreparation;

        return $this;
    }

    public function getTimeCooking(): ?int
    {
        return $this->timeCooking;
    }

    public function setTimeCooking(?int $timeCooking): self
    {
        $this->timeCooking = $timeCooking;

        return $this;
    }

    public function getCalorie(): ?int
    {
        return $this->calorie;
    }

    public function setCalorie(?int $calorie): self
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

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSteps(): ?Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
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

    public function addIngredient(RecipeIngredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(RecipeIngredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipe() === $this) {
                $ingredient->setRecipe(null);
            }
        }

        return $this;
    }


    /**
     * Calcul de nombre de calories de la recette par personne
     * 
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function calculCalories(): ?int
    {
        $calories = 0;

        // Pour chaque ingrédient de la recette
        foreach ($this->ingredients as $ingredient) {
            $cal = $ingredient->getCalories();
            if ( $cal === null ) {
                // Manque les calories d'un élément donc impossible de calculer
                $this->calorie = null;
                return null;
            }
            $calories+= $cal;
        }

        // Nombre de calories par personne
        $this->calorie = round($calories / $this->person);
        return $this->calorie;
    }

}