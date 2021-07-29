<?php
/**
 * Classe statique sur les niveaux de difficultés
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Constant;


class Difficulty
{

    /**
     * Constantes des niveaux de difficulté
     */
    const EASY   = 1;
    const MEDIUM = 2;
    const HARD   = 3;

    static private $difficulties = [
        self::EASY   => 'Facile',
        self::MEDIUM => 'Moyen',
        self::HARD   => 'Difficile',
    ];


    /**
     * @var Integer
     */
    private $difficulty;


    /**
     * Constructeur
     */
    public function __construct(int $difficulty)
    {
        $this->difficulty = $difficulty;
    }


    /**
     * Retourne le label
     */
    public function __toString()
    {
        return $this->getLabel();
    }


    /**
     * Retourne la valeur
     */
    public function getValue(): int
    {
        return $this->difficulty;
    }


    /**
     * Retourne le label
     */
    public function getLabel(): string
    {
        return self::$difficulties[$this->difficulty];
    }


    /**
     * Retourne la liste des niveaux de difficulté
     */
    static public function getConstants(): array
    {
        return self::$difficulties;
    }


    /**
     * Retourne la liste pour les formulaires de type "choices"
     */
    static public function getChoices(): array
    {
        $result = [];
        foreach (self::$difficulties as $key => $value) {
            $result[] = new self($key);
        }
        return $result;
    }

}
