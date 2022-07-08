<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Constant;

use Exception;

/**
 * Classe statique sur les niveaux de difficultés.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class Difficulty
{
    /**
     * Constantes des niveaux de difficulté.
     */
    public const EASY = 1;
    public const MEDIUM = 2;
    public const HARD = 3;

    /**
     * @var array<string>
     */
    private static $difficulties = [
        self::EASY => 'Facile',
        self::MEDIUM => 'Moyen',
        self::HARD => 'Difficile',
    ];

    /**
     * @var int
     */
    private $difficulty;

    /**
     * Constructeur.
     */
    public function __construct(int $difficulty)
    {
        if (!array_key_exists($difficulty, self::$difficulties)) {
            throw new Exception('La valeur "'.$difficulty.'" est inconue, Valeur possible : '.implode(',', array_keys(self::$difficulties)));
        }
        $this->difficulty = $difficulty;
    }

    /**
     * Retourne le label.
     */
    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * Retourne la valeur.
     */
    public function getValue(): int
    {
        return $this->difficulty;
    }

    /**
     * Retourne le label.
     */
    public function getLabel(): string
    {
        return self::$difficulties[$this->difficulty];
    }

    /**
     * Retourne la liste des niveaux de difficulté.
     *
     * @return array<string>
     */
    public static function getConstants(): array
    {
        return self::$difficulties;
    }

    /**
     * Retourne la liste pour les formulaires de type "choices".
     *
     * @return array<Difficulty>
     */
    public static function getChoices(): array
    {
        $result = [];
        foreach (array_keys(self::$difficulties) as $key) {
            $result[] = new self($key);
        }

        return $result;
    }
}
