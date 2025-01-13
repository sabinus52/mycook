<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\ValuesList;

use Olix\BackOfficeBundle\ValuesList\ValuesListAbstract;

/**
 * Classe statique sur les niveaux de difficultés.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class Difficulty extends ValuesListAbstract
{
    public const EASY = 1;
    public const MEDIUM = 2;
    public const HARD = 3;

    /**
     * @var array<int,array<string,string>>
     */
    protected static array $values = [
        self::EASY => ['label' => 'Facile'],
        self::MEDIUM => ['label' => 'Moyen'],
        self::HARD => ['label' => 'Difficile'],
    ];
}
