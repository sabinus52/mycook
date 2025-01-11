<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\ValuesList;

/**
 * Classe statique sur les unités des quantités.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class Unity implements \Stringable
{
    /**
     * Constantes des niveaux de difficulté.
     */
    public const WEIGHT = 'weight';
    public const CAPACITY = 'capacity';
    public const TNUMBER = 'number';

    public const NUMBER = '';
    public const KILO = 'Kg';
    public const GRAM = 'g';
    public const OUNCE = 'oz';
    public const POUND = 'lb';
    public const CUP = 'c.';
    public const TABLESPOON = 'T.';
    public const TEASPOON = 't.';
    public const LITRE = 'l';
    public const DLITRE = 'dl';
    public const CLITRE = 'cl';
    public const MLITRE = 'ml';

    /**
     * @var array<string,array<string,string|float>>
     */
    private static array $unities = [
        self::NUMBER => ['type' => self::TNUMBER,   'label' => 'nombre',               'symbol' => '',      'conversion' => 0],
        self::KILO => ['type' => self::WEIGHT,    'label' => 'kilo(s)',              'symbol' => 'Kg',    'conversion' => 1000],
        self::GRAM => ['type' => self::WEIGHT,    'label' => 'gramme(s)',            'symbol' => 'g',     'conversion' => 1],
        self::OUNCE => ['type' => self::WEIGHT,    'label' => 'ounce(s)',             'symbol' => 'oz',    'conversion' => 28.34],
        self::POUND => ['type' => self::WEIGHT,    'label' => 'pound(s)',             'symbol' => 'lb',    'conversion' => 455],
        self::CUP => ['type' => self::WEIGHT,    'label' => 'tasse(s)',             'symbol' => 'c.',    'conversion' => 250],
        self::TABLESPOON => ['type' => self::WEIGHT,    'label' => 'cuillère(s) à soupe',  'symbol' => 'T.',    'conversion' => 15],
        self::TEASPOON => ['type' => self::WEIGHT,    'label' => 'cuillère(s) à café',   'symbol' => 't.',    'conversion' => 5],
        self::LITRE => ['type' => self::CAPACITY,  'label' => 'litre(s)',             'symbol' => 'l',     'conversion' => 1000],
        self::DLITRE => ['type' => self::CAPACITY,  'label' => 'décilitre(s)',         'symbol' => 'dl',    'conversion' => 100],
        self::CLITRE => ['type' => self::CAPACITY,  'label' => 'centilitre(s)',        'symbol' => 'cl',    'conversion' => 10],
        self::MLITRE => ['type' => self::CAPACITY,  'label' => 'millilitre(s)',        'symbol' => 'ml',    'conversion' => 1],
    ];

    private readonly string $unity;

    /**
     * Constructeur.
     */
    public function __construct(string $unity)
    {
        if (!array_key_exists($unity, self::$unities)) {
            throw new \Exception('La valeur "'.$unity.'" est inconnue, Valeur possible : '.implode(',', array_keys(self::$unities)));
        }
        $this->unity = $unity;
    }

    /**
     * Retourne le label.
     */
    #[\Override]
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * Determine si l'unité est un nombre ou une mesure.
     */
    public function isNumber(): bool
    {
        return self::NUMBER === $this->unity;
    }

    /**
     * Retourne la valeur.
     */
    public function getValue(): string
    {
        return $this->unity;
    }

    /**
     * Retourne le label.
     */
    public function getLabel(): string
    {
        return (string) self::$unities[$this->unity]['label'];
    }

    /**
     * Retourne le symbole.
     */
    public function getSymbol(): string
    {
        return (string) self::$unities[$this->unity]['symbol'];
    }

    /**
     * Retourne la conversion en grammes.
     */
    public function getConversion(): float
    {
        return (float) self::$unities[$this->unity]['conversion'];
    }

    /**
     * Retourne la liste des unité.
     *
     * @return array<string,array<string,string|float>>
     */
    public static function getConstants(): array
    {
        return self::$unities;
    }

    /**
     * Retourne la liste pour les formulaires de type "choices".
     *
     * @return array<Unity>
     */
    public static function getChoices(): array
    {
        $result = [];
        foreach (array_keys(self::$unities) as $key) {
            $result[] = new self($key);
        }

        return $result;
    }

    /**
     * Retourne le grammage à partir d'une quantité.
     */
    public function getInGram(float $quantity): float
    {
        if ($this->isNumber()) {
            throw new \Exception('Impossible de convertir un nombre en gramme');
        }

        return $quantity * $this->getConversion();
    }

    /**
     * Convertit à partir de sa propre unité vers la nouvelle unité.
     *
     * @param float  $quantity : Quantité à convertir
     * @param string $unity    : Unité cible convertie
     */
    public function convert(float $quantity, string $unity): float
    {
        $target = new self($unity);

        if ($this->isNumber() || $target->isNumber() || 0.0 === $target->getConversion()) {
            throw new \Exception('Impossible de convertir un nombre');
        }
        $gram = $this->getInGram($quantity);

        return round($gram / $target->getConversion(), 2);
    }
}
