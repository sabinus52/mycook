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
 * Classe statique sur les niveaux de coûts.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class Rate extends ValuesListAbstract
{
    public const CHEAP = 1;
    public const MEDIUM = 2;
    public const EXPENSIVE = 3;

    /**
     * @var array<int,array<string,string>>
     */
    protected static array $values = [
        self::CHEAP => ['label' => 'Bon marché'],
        self::MEDIUM => ['label' => 'Moyen'],
        self::EXPENSIVE => ['label' => 'Cher'],
    ];
}
