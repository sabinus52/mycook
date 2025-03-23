<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\Step;
use App\Enum\Difficulty;
use App\Enum\Rate;
use App\Enum\Unity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Données initiales de l’application pour les recettes.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class RecipeFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->loadRecipe1($manager);
        $this->loadRecipe2($manager);
        $this->loadRecipe3($manager);
    }

    private function loadRecipe1(ObjectManager $manager): void
    {
        $repo = $manager->getRepository(Ingredient::class);

        $recipe = new Recipe();
        $recipe
            ->setName('Fondant chocolat mascarpone')
            ->setPerson(12)
            ->setDifficulty(Difficulty::EASY)
            ->setRate(Rate::MEDIUM)
            ->setTimePreparation(10)
            ->setTimeCooking(25)
        ;

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Chocolat']))
            ->setQuantity(200)
            ->setUnity(Unity::GRAM)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Mascarpone']))
            ->setQuantity(1)
            ->setUnity(Unity::CUP)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Oeuf']))
            ->setQuantity(4)
            ->setUnity(Unity::NUMBER)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Farine']))
            ->setQuantity(40)
            ->setUnity(Unity::GRAM)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Sucre']))
            ->setQuantity(0)
            ->setUnity(Unity::GRAM)
            ->setNote('1 sachet de sucre vanillé')
        ;
        $recipe->addIngredient($ingredient);

        $step = new Step();
        $recipe->addStep($step->setContent('Préchauffez le four à 150° (th5)'));
        $step = new Step();
        $recipe->addStep($step->setContent('Faites fondre le chocolat au bain-marie, ou au micro-ondes (à faible puissance et par courtes périodes).'));
        $step = new Step();
        $recipe->addStep($step->setContent('Versez la mascarpone sur le chocolat fondu sur le mascarpone et fouettez jusqu\'à ce qu\'il soit bien incorporé.'));
        $step = new Step();
        $recipe->addStep($step->setContent('Ajoutez les oeufs entiers, un par un, en fouettant. (Attendez que la pâte soit homogène avant d\'ajouter l\'oeuf suivant)'));
        $step = new Step();
        $recipe->addStep($step->setContent('Versez ensuite le sucre glace et fouettez à nouveau.'));
        $step = new Step();
        $recipe->addStep($step->setContent('Versez la farine et mélangez délicatement avec une Maryse ou un fouet à main. (Il ne faut pas trop rajouter d\'air dans la pâte sinon le gâteau gonflera à la cuisson, il doit rester plat.)'));
        $step = new Step();
        $recipe->addStep($step->setContent('Versez la pâte dans le moule et enfournez pour 25 minutes.'));
        $step = new Step();
        $recipe->addStep($step->setContent('A la sortie du four, laissez refroidir le gâteau dans le moule. Quand il est à température ambiante, réfrigérez le 3h minimum.'));

        $manager->persist($recipe);
        $manager->flush();
    }

    private function loadRecipe2(ObjectManager $manager): void
    {
        $repo = $manager->getRepository(Ingredient::class);

        $recipe = new Recipe();
        $recipe
            ->setName('Saucisse de Morteau aux pommes de terre')
            ->setPerson(4)
            ->setDifficulty(Difficulty::EASY)
            ->setRate(Rate::CHEAP)
            ->setTimePreparation(15)
            ->setTimeCooking(40)
        ;

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Saucisse de Morteau']))
            ->setQuantity(1)
            ->setUnity(Unity::NUMBER)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Pomme de terre']))
            ->setQuantity(800)
            ->setUnity(Unity::GRAM)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Oignon']))
            ->setQuantity(2)
            ->setUnity(Unity::NUMBER)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Sel']))
            ->setUnity(Unity::GRAM)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Poivre']))
            ->setUnity(Unity::GRAM)
        ;
        $recipe->addIngredient($ingredient);

        $step = new Step();
        $recipe->addStep($step->setContent('Peler et couper les pommes de terre et les oignons en tranches fines. Couper également le saucisson en tranches.'));
        $step = new Step();
        $recipe->addStep($step->setContent('Dans une cocotte, légèrement graissée, mettre alternativement des couches de saucisson, d\'oignons et de pommes de terre. Saler, poivrer'));
        $step = new Step();
        $recipe->addStep($step->setContent('Couvrir et laisser cuire à feu tout doux pendant 40 minutes environ'));

        $manager->persist($recipe);
        $manager->flush();
    }

    private function loadRecipe3(ObjectManager $manager): void
    {
        $repo = $manager->getRepository(Ingredient::class);

        $recipe = new Recipe();
        $recipe
            ->setName('Asperges vertes au jambon et gorgonzola')
            ->setPerson(2)
            ->setDifficulty(Difficulty::EASY)
            ->setRate(Rate::CHEAP)
            ->setTimePreparation(15)
            ->setTimeCooking(20)
        ;

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Asperge verte']))
            ->setQuantity(500)
            ->setUnity(Unity::GRAM)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Jambon']))
            ->setQuantity(3)
            ->setUnity(Unity::NUMBER)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Gorgonzola']))
            ->setQuantity(150)
            ->setUnity(Unity::GRAM)
        ;
        $recipe->addIngredient($ingredient);

        $ingredient = new RecipeIngredient();
        $ingredient
            ->setIngredient($repo->findOneBy(['name' => 'Crème fraîche']))
            ->setQuantity(20)
            ->setUnity(Unity::CLITRE)
        ;
        $recipe->addIngredient($ingredient);

        $step = new Step();
        $recipe->addStep($step->setContent('Éplucher et faire cuire 10 min les asperges dans de l\'eau bouillante.'));
        $step = new Step();
        $recipe->addStep($step->setContent('Préchauffez votre four à 190°C'));
        $step = new Step();
        $recipe->addStep($step->setContent('Coupez le jambon en petits morceaux.'));
        $step = new Step();
        $recipe->addStep($step->setContent('Dans une casserole, mettez le gorgonzola et la crème fraîche et faites fondre doucement.'));
        $step = new Step();
        $recipe->addStep($step->setContent('Dans un plat allant au four, placez les asperges, les morceaux de jambon par dessus, puis recouvrez de la crème au gorgonzola. Enfournez 25 à 30 minutes, jusqu\'à ce que ça soit bien doré.'));

        $manager->persist($recipe);
        $manager->flush();
    }
}
