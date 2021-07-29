<?php
/**
 * Type personnalisé de mapping pour les niveaux de difficulté d'une recette
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Entity\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use App\Constant\Difficulty;


class DifficultyType extends Type
{

    const DIFFICULTY = 'difficulty';


    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'smallint';
    }


    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new Difficulty($value);
    }


    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getValue();
    }


    public function getName()
    {
        return self::DIFFICULTY;
    }

}