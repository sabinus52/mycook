<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Constant;

/**
 * Classe statique sur les niveaux de couts.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class Rate implements \Stringable
{
    /**
     * Constantes des niveaux de difficulté.
     */
    public const CHEAP = 1;
    public const MEDIUM = 2;
    public const EXPENSIVE = 3;

    /**
     * @var array<string>
     */
    private static $rates = [
        self::CHEAP => 'Bon marché',
        self::MEDIUM => 'Moyen',
        self::EXPENSIVE => 'Cher',
    ];

    /**
     * @var int
     */
    private $rate;

    /**
     * Constructeur.
     */
    public function __construct(int $rate)
    {
        if (!array_key_exists($rate, self::$rates)) {
            throw new \Exception('La valeur "'.$rate.'" est inconue, Valeur possible : '.implode(',', array_keys(self::$rates)));
        }
        $this->rate = $rate;
    }

    /**
     * Retourne le label.
     */
    public function __toString(): string
    {
        return $this->getLabel();
    }

    /**
     * Retoune la valeur.
     */
    public function getValue(): int
    {
        return $this->rate;
    }

    /**
     * Retourne le label.
     */
    public function getLabel(): string
    {
        return self::$rates[$this->rate];
    }

    /**
     * Retourne la liste des niveaux de cout.
     *
     * @return array<string>
     */
    public static function getConstants(): array
    {
        return self::$rates;
    }

    /**
     * Retourne la liste pour les formulaires de type "choices".
     *
     * @return array<Rate>
     */
    public static function getChoices(): array
    {
        $result = [];
        foreach (array_keys(self::$rates) as $key) {
            $result[] = new self($key);
        }

        return $result;
    }
}
