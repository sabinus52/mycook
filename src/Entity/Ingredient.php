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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
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
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * Jointure avec les recettes
     * 
     * @ORM\OneToMany(targetEntity=RecipeIngredient::class, mappedBy="ingredient")
     */
    private $recipes;

    
    public function __construct()
    {
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

}
