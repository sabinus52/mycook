<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Tests\Entity;

use App\Constant\Unity;
use App\Entity\Ingredient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires des Ingrédients.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @internal
 * @coversNothing
 */
final class IngredientTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    /**
     * Test des unités de conversion à partir du gramme.
     */
    public function testGram(): void
    {
        $unity = new Unity(Unity::GRAM);

        $this->AssertFalse($unity->isNumber());
        static::assertSame($unity->getSymbol(), 'g');
        static::assertSame($unity->getLabel(), 'gramme(s)');
        static::assertSame($unity->getInGram(100), 100);
        static::assertSame($unity->convert(250, Unity::CUP), 1);
        static::assertSame($unity->convert(250, Unity::OUNCE), 8.82);
    }

    /**
     * Test de l'unité Nombre.
     */
    public function testNumber(): void
    {
        $unity = new Unity(Unity::NUMBER);

        $this->AssertTrue($unity->isNumber());
        static::assertSame($unity->getSymbol(), '');
        static::assertSame($unity->getLabel(), 'nombre');
    }

    /**
     * @dataProvider additionProvider
     *
     * @param mixed $ingredient
     * @param mixed $quantity
     * @param mixed $mass
     * @param mixed $calorie
     */
    public function testConversionInGramAndCalories($ingredient, $quantity, Unity $source, $mass, $calorie): void
    {
        $ingredient = $this->entityManager->getRepository(Ingredient::class)->findOneByName($ingredient);
        static::assertSame($ingredient->getInGram($quantity, $source), $mass);
        static::assertSame($ingredient->getCalories($quantity, $source), $calorie);
    }

    /**
     * Données des ingrédients.
     */
    public function additionProvider(): array
    {
        return [
            // Ingredient, qt,     unité,                    poids, calories
            ['Oeuf',     100,    new Unity(Unity::GRAM),     100, 155],  // 0
            ['Beurre',   100,    new Unity(Unity::GRAM),     100, 717],
            ['Lait',     100,    new Unity(Unity::GRAM),     100, 46],
            ['Sucre',    100,    new Unity(Unity::GRAM),     100, 400],
            ['Sel',      null,   new Unity(Unity::GRAM),     0,   null],
            ['Oignon',   100,    new Unity(Unity::GRAM),     100, 37],  // 5
            ['Poivre',   null,   new Unity(Unity::GRAM),     0,   0],
            ['Banane',   100,    new Unity(Unity::GRAM),     100, 90],
            ['Oeuf',     1,      new Unity(Unity::CUP),      250, 388],
            ['Beurre',   1,      new Unity(Unity::CUP),      250, 1793],
            ['Lait',     1,      new Unity(Unity::CUP),      250, 115],  // 10
            ['Sucre',    1,      new Unity(Unity::CUP),      250, 1000],
            ['Sel',      null,   new Unity(Unity::CUP),      0,   null],
            ['Oignon',   1,      new Unity(Unity::CUP),      250, 93],
            ['Poivre',   null,   new Unity(Unity::CUP),      0,   0],
            ['Banane',   1,      new Unity(Unity::CUP),      250, 225],  // 15
            ['Oeuf',     1,      new Unity(Unity::NUMBER),   60, 93],
            ['Beurre',   1,      new Unity(Unity::NUMBER),   null, null],
            ['Lait',     1,      new Unity(Unity::NUMBER),   null, null],
            ['Sucre',    1,      new Unity(Unity::NUMBER),   null, null],
            ['Sel',      null,   new Unity(Unity::NUMBER),   null, null], // 20
            ['Poivre',   null,   new Unity(Unity::NUMBER),   null, null],
            ['Oignon',   1,      new Unity(Unity::NUMBER),   100, 37],
            ['Banane',   1,      new Unity(Unity::NUMBER),   120, 108],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
