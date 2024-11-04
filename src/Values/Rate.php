<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Values;

use Olix\BackOfficeBundle\Values\ValuesAbstract;

/**
 * Classe statique sur les niveaux de couts.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class Rate extends ValuesAbstract
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
    protected static $values = [
        self::CHEAP => 'Bon marché',
        self::MEDIUM => 'Moyen',
        self::EXPENSIVE => 'Cher',
    ];

    /**
     * Constructeur.
     */
    public function __construct(protected int $value)
    {
        if (!array_key_exists($value, self::$values)) {
            throw new \Exception('La valeur "'.$value.'" est inconue, Valeur possible : '.implode(',', array_keys(self::$values)));
        }
    }
}
