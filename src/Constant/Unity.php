<?php
/**
 * Classe statique sur les unités des quantités
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Constant;


class Unity
{

    /**
     * Constantes des niveaux de difficulté
     */
    const WEIGHT   = 'weight';
    const CAPACITY = 'capacity';
    const TNUMBER  = 'number';

    const NUMBER        = '';
    const KILO          = 'Kg';
    const GRAM          = 'g';
    const OUNCE         = 'oz';
    const POUND         = 'lb';
    const CUP           = 'c.';
    const TABLESPOON    = 'T.';
    const TEASPOON      = 't.';
    const LITRE         = 'l';
    const DLITRE        = 'dl';
    const CLITRE        = 'cl';
    const MLITRE        = 'ml';


    static private $unities = [
        self::NUMBER        => [ 'type' => self::TNUMBER,   'label' => 'nombre',               'symbol' => '' ],
        self::KILO          => [ 'type' => self::WEIGHT,    'label' => 'kilo(s)',              'symbol' => 'Kg' ],
        self::GRAM          => [ 'type' => self::WEIGHT,    'label' => 'gramme(s)',            'symbol' => 'g' ],
        self::OUNCE         => [ 'type' => self::WEIGHT,    'label' => 'ounce(s)',             'symbol' => 'oz' ],
        self::POUND         => [ 'type' => self::WEIGHT,    'label' => 'pound(s)',             'symbol' => 'lb' ],
        self::CUP           => [ 'type' => self::WEIGHT,    'label' => 'tasse(s)',             'symbol' => 'c.' ],
        self::TABLESPOON    => [ 'type' => self::WEIGHT,    'label' => 'cuillère(s) à soupe',  'symbol' => 'T.' ],
        self::TEASPOON      => [ 'type' => self::WEIGHT,    'label' => 'cuillère(s) à café',   'symbol' => 't.' ],
        self::LITRE         => [ 'type' => self::CAPACITY,  'label' => 'litre(s)',             'symbol' => 'l' ],
        self::DLITRE        => [ 'type' => self::CAPACITY,  'label' => 'décilitre(s)',         'symbol' => 'dl' ],
        self::CLITRE        => [ 'type' => self::CAPACITY,  'label' => 'centilitre(s)',        'symbol' => 'cl' ],
        self::MLITRE        => [ 'type' => self::CAPACITY,  'label' => 'millilitre(s)',        'symbol' => 'ml' ],
    ];


    /**
     * @var String
     */
    private $unity;


    /**
     * Constructeur
     */
    public function __construct(string $unity)
    {
        $this->unity = $unity;
    }


    /**
     * Retourne le label
     */
    public function __toString()
    {
        return $this->getValue();
    }


    /**
     * Retoune la valeur
     */
    public function getValue(): string
    {
        return $this->unity;
    }


    /**
     * Retourne le label
     */
    public function getLabel(): string
    {
        return self::$unities[$this->unity]['label'];
    }


    /**
     * Retourne le symbole
     */
    public function getSymbol(): string
    {
        return self::$unities[$this->unity]['symbol'];
    }


    /**
     * Retourne la liste des niveaux de cout
     */
    static public function getConstants(): array
    {
        return self::$unities;
    }


    /**
     * Retourne la liste pour les formulaires de type "choices"
     */
    static public function getChoices(): array
    {
        $result = [];
        foreach (self::$unities as $key => $value) {
            $result[] = new self($key);
        }
        return $result;
    }

}
