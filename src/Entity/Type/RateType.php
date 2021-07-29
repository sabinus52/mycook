<?php
/**
 * Type personnalisé de mapping pour les niveaux de coût d'une recette
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use App\Constant\Rate;


class RateType extends Type
{

    const RATE = 'rate';


    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'smallint';
    }


    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new Rate($value);
    }


    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getValue();
    }


    public function getName()
    {
        return self::RATE;
    }

}