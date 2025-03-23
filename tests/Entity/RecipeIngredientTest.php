<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Entity;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Enum\Unity;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires des ingrédients de recettes.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 */
final class RecipeIngredientTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        // @phpstan-ignore-next-line
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    /**
     * @dataProvider provideGetCaloriesCases
     */
    public function testGetCalories(string $name, int $quantity, Unity $unit, ?int $weight, ?int $calorie): void
    {
        $recipe = new Recipe();

        $ingredient = $this->entityManager->getRepository(Ingredient::class)->findOneBy(['name' => $name]);

        $recipeIngredient = new RecipeIngredient();
        $recipeIngredient->setRecipe($recipe);
        $recipeIngredient->setIngredient($ingredient);
        $recipeIngredient->setQuantity($quantity);
        $recipeIngredient->setUnity($unit);

        self::assertSame($weight, $recipeIngredient->getWeightInGram());
        self::assertSame($calorie, $recipeIngredient->getCalories());
    }

    /**
     * @return array<int,array<string|int|Unity|null>>
     */
    public static function provideGetCaloriesCases(): iterable
    {
        return [
            // Ingredient, qt,     unité,                    poids, calories
            ['Oeuf',     100,    Unity::GRAM,     100, 155],  // 0
            ['Beurre',   100,    Unity::GRAM,     100, 717],
            ['Lait',     100,    Unity::GRAM,     100, 46],
            ['Sucre',    100,    Unity::GRAM,     100, 400],
            ['Sel',      0,      Unity::GRAM,     0,   null],
            ['Oignon',   100,    Unity::GRAM,     100, 37],  // 5
            ['Poivre',   0,      Unity::GRAM,     0,   0],
            ['Banane',   100,    Unity::GRAM,     100, 90],
            ['Oeuf',     1,      Unity::CUP,      250, 388],
            ['Beurre',   1,      Unity::CUP,      250, 1793],
            ['Lait',     1,      Unity::CUP,      250, 115],  // 10
            ['Sucre',    1,      Unity::CUP,      250, 1000],
            ['Sel',      0,      Unity::CUP,      0,   null],
            ['Oignon',   1,      Unity::CUP,      250, 93],
            ['Poivre',   0,      Unity::CUP,      0,   0],
            ['Banane',   1,      Unity::CUP,      250, 225],  // 15
            ['Oeuf',     1,      Unity::NUMBER,   60, 93],
            ['Beurre',   1,      Unity::NUMBER,   null, null],
            ['Lait',     1,      Unity::NUMBER,   null, null],
            ['Sucre',    1,      Unity::NUMBER,   null, null],
            ['Sel',      0,      Unity::NUMBER,   null, null], // 20
            ['Poivre',   0,      Unity::NUMBER,   null, null],
            ['Oignon',   1,      Unity::NUMBER,   100, 37],
            ['Banane',   1,      Unity::NUMBER,   120, 108],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
