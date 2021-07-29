<?php
/**
 * Tests unitaires des Ingrédients
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Tests\Entity;

use App\Constant\Unity;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class RecipeTest extends KernelTestCase
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
            ->getManager();
    }


    /**
     * @dataProvider additionProviderCalorie
     */
    public function testCalculCalories($recipe, $calorie): void
    {
        $recipe = $this->entityManager->getRepository(Recipe::class)->findOneByName($recipe);
        $this->assertSame($recipe->calculCalories(), $calorie);
    }


    /**
     * Données pour test des calculs de calories
     */
    public function additionProviderCalorie(): array
    {
        return [
            [ 'Fondant chocolat mascarpone', 205 ],
            [ 'Saucisse de Morteau aux pommes de terre', null ], // 512
            [ 'Asperges vertes au jambon et gorgonzola', 765 ], // 512
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