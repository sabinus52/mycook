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
 * Énumération sur les niveaux de difficulté.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
enum Difficulty: int
{
    case EASY = 1;
    case MEDIUM = 2;
    case HARD = 3;

    public function label(): string
    {
        return match ($this) {
            self::EASY => 'Facile',
            self::MEDIUM => 'Moyen',
            self::HARD => 'Difficile',
        };
    }
}
