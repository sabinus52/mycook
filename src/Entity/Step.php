<?php
/**
 * Entité des étapes composant la recette
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity;

use App\Repository\StepRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StepRepository::class)
 */
class Step
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Contenu ou description de l'étape
     * 
     * @var String
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * Jointure avec les recettes
     * 
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="steps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;


    public function __construct()
    {
        $this->steps = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }


    public function getRecipe(): ?self
    {
        return $this->recipe;
    }

    public function setRecipe(?self $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

}
