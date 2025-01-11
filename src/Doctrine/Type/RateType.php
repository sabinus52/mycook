<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Doctrine\Type;

use App\ValuesList\Rate;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Type personnalisé de mapping pour les niveaux de coût d'une recette.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RateType extends Type
{
    public const RATE = 'rate';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'smallint';
    }

    #[\Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Rate
    {
        if (null === $value) {
            return null;
        }

        return new Rate((int) $value); // @phpstan-ignore cast.int
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (!$value instanceof Rate) {
            return null;
        }

        return $value->getValue();
    }

    #[\Override]
    public function getName(): string
    {
        return self::RATE;
    }
}
