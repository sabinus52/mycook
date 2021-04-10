<?php
/**
 * Entité des unités
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity;

use App\Repository\UnityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UnityRepository::class)
 */
class Unity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Nom de l'unité
     * 
     * @var String
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * Symbole de l'unité
     * 
     * @var String
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $symbol;


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


    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

}
