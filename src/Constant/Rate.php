<?php
/**
 * Classe statique sur les niveaux de couts
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Constant;


class Rate
{

    /**
     * Constantes des niveaux de difficulté
     */
    const CHEAP     = 1;
    const MEDIUM    = 2;
    const EXPENSIVE = 3;

    static private $rates = [
        self::CHEAP     => 'Bon marché',
        self::MEDIUM    => 'Moyen',
        self::EXPENSIVE => 'Cher',
    ];


    /**
     * @var Integer
     */
    private $rate;


    /**
     * Constructeur
     */
    public function __construct(int $rate)
    {
        $this->rate = $rate;
    }


    /**
     * Retourne le label
     */
    public function __toString()
    {
        return $this->getLabel();
    }


    /**
     * Retoune la valeur
     */
    public function getValue(): int
    {
        return $this->rate;
    }


    /**
     * Retourne le label
     */
    public function getLabel(): string
    {
        return self::$rates[$this->rate];
    }


    /**
     * Retourne la liste des niveaux de cout
     */
    static public function getConstants(): array
    {
        return self::$rates;
    }


    /**
     * Retourne la liste pour les formulaires de type "choices"
     */
    static public function getChoices(): array
    {
        $result = [];
        foreach (self::$rates as $key => $value) {
            $result[] = new self($key);
        }
        return $result;
    }

}
