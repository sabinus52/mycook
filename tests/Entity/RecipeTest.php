<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Tests\Entity;

use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires des Ingrédients.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 */
final class RecipeTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

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
     * @dataProvider provideCalculCaloriesCases
     *
     * @param mixed $recipe
     * @param mixed $calorie
     */
    public function testCalculCalories($recipe, $calorie): void
    {
        /** @phpstan-ignore-next-line */
        $recipe = $this->entityManager->getRepository(Recipe::class)->findOneByName($recipe);
        self::assertSame($recipe->calculCalories(), $calorie);
    }

    /**
     * Données pour test des calculs de calories.
     *
     * @return array<mixed>
     */
    public static function provideCalculCaloriesCases(): iterable
    {
        return [
            ['Fondant chocolat mascarpone', 205],
            ['Saucisse de Morteau aux pommes de terre', null], // 512
            ['Asperges vertes au jambon et gorgonzola', 765], // 512
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
    }
}
