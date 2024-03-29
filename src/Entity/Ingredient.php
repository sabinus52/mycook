<?php
/**
 * Entité des ingrédients
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constant\Unity;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 * @UniqueEntity("name")
 */
class Ingredient
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Nom de l'ingrédient
     * 
     * @var String
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="unity")
     */
    private $unity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $conversion;

    /**
     * Nombre de calorie pour 100 gramme
     * 
     * @var Integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $calorie;

    /**
     * Jointure avec les recettes
     * 
     * @ORM\OneToMany(targetEntity=RecipeIngredient::class, mappedBy="ingredient")
     */
    private $recipes;

    
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

    public function setName(string $name): self
    {
        $this->name = $name;

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


    public function getConversion(): ?string
    {
        return $this->conversion;
    }

    public function setConversion(?string $conversion): self
    {
        $this->conversion = $conversion;

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
     * @return Collection|RecipeIngredient[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(RecipeIngredient $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setIngredient($this);
        }

        return $this;
    }

    public function removeRecipe(RecipeIngredient $recipe): self
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
     * Retourne le poids en gramme de l'ingrédient à partir d'une certaine quantité
     * 
     * @param Float $quantity : Quantity de l'ingrédient
     * @param Unity $source   : Unité de la quantité
     */
    public function getInGram(?float $quantity, Unity $source): ?int
    {
        if ( ! $source->isNumber() ) {
            // Si pas un nombre, on peut convertir directement en gramme
            return $source->getInGram($quantity);
        } else {
            // Si un nombre, on vérifie si on peut convertir
            if ( $this->conversion === null ) return null;
            return round($this->conversion * $quantity);
        }
    }


    /**
     * Retourne le nombre de calorie de l'ingrédient à partir d'une certaine quantité
     * 
     * @param Float $quantity : Quantity de l'ingrédient
     * @param Unity $source   : Unité de la quantité
     */
    public function getCalories(?float $quantity, Unity $source): ?int
    {
        if ( $this->calorie === null ) return null;

        // Poids en gramme de l'ingrédient
        $mass = $this->getInGram($quantity, $source);
        if ( $mass === null ) return null;

        return round($mass * $this->calorie / 100);
    }

}