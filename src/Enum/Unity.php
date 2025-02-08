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
 * Énumération sur les unités.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
enum Unity: string
{
    case NUMBER = 'U';
    case GRAM = 'g';
    case KILO = 'Kg';
    case OUNCE = 'oz';
    case POUND = 'lb';
    case CUP = 'c.';
    case TABLESPOON = 'T.';
    case TEASPOON = 't.';
    case LITRE = 'l';
    case DLITRE = 'dl';
    case CLITRE = 'cl';
    case MLITRE = 'ml';

    public function label(): string
    {
        return match ($this) {
            self::NUMBER => 'nombre',
            self::GRAM => 'gramme(s)',
            self::KILO => 'kilo(s)',
            self::OUNCE => 'ounce(s)',
            self::POUND => 'pound(s)',
            self::CUP => 'tasse(s)',
            self::TABLESPOON => 'cuillère(s) à soupe',
            self::TEASPOON => 'cuillère(s) à café',
            self::LITRE => 'litre(s)',
            self::DLITRE => 'décilitre(s)',
            self::CLITRE => 'centilitre(s)',
            self::MLITRE => 'millilitre(s)',
        };
    }

    public function symbol(): string
    {
        if (self::NUMBER === $this) {
            return '';
        }

        return $this->value;
    }

    public function conversion(): float
    {
        return match ($this) {
            self::NUMBER => 0,
            self::GRAM => 1,
            self::KILO => 1000,
            self::OUNCE => 28.34,
            self::POUND => 455,
            self::CUP => 250,
            self::TABLESPOON => 15,
            self::TEASPOON => 5,
            self::LITRE => 1000,
            self::DLITRE => 100,
            self::CLITRE => 10,
            self::MLITRE => 1,
        };
    }

    public function isNumber(): bool
    {
        return self::NUMBER === $this;
    }

    public function inGram(float $quantity): float
    {
        if ($this->isNumber()) {
            throw new \Exception('Impossible de convertir un nombre en gramme');
        }

        return $quantity * $this->conversion();
    }

    /**
     * @deprecated 2
     */
    public function convert(float $quantity, Unity $target): float
    {
        if ($this->isNumber() || $target->isNumber() || 0.0 === $target->conversion()) {
            throw new \Exception('Impossible de convertir un nombre');
        }
        $gram = $this->inGram($quantity);

        return round($gram / $target->conversion(), 2);
    }
}
