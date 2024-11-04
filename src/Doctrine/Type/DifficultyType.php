<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Doctrine\Type;

use App\Values\Difficulty;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Type personnalisé de mapping pour les niveaux de difficulté d'une recette.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DifficultyType extends Type
{
    public const DIFFICULTY = 'difficulty';

    #[\Override]
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'smallint';
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Difficulty
    {
        if (null === $value) {
            return null;
        }

        return new Difficulty((int) $value);
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if (null === $value) {
            return null;
        }

        return $value->getValue();
    }

    #[\Override]
    public function getName(): string
    {
        return self::DIFFICULTY;
    }
}
