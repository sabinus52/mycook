<?php
/**
 * Type personnalisé de mapping pour des unités dea quantités des ingrédients
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use App\Constant\Unity;


class UnityType extends Type
{

    const UNITY = 'unity';


    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'VARCHAR(2)';
    }


    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new Unity($value);
    }


    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getValue();
    }


    public function getName()
    {
        return self::UNITY;
    }

}