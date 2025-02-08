<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enum;

/**
 * Énumération sur les niveaux de coûts.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
enum Rate: int
{
    case CHEAP = 1;
    case MEDIUM = 2;
    case EXPENSIVE = 3;

    public function label(): string
    {
        return match ($this) {
            self::CHEAP => 'Bon marché',
            self::MEDIUM => 'Moyen',
            self::EXPENSIVE => 'Cher',
        };
    }
}
