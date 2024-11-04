<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Constant\Unity;
use App\Entity\Category;
use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Données initiales de l'applicaton.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadCategories($manager);
        $this->loadIngredients($manager);
    }

    /**
     * Chargement des catégories.
     */
    private function loadCategories(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName('Entrées');

        $manager->persist($category);

        $category = new Category();
        $category->setName('Plats');

        $manager->persist($category);

        $category = new Category();
        $category->setName('Desserts');

        $manager->persist($category);

        $manager->flush();
    }

    /**
     * Chargement des ingrédients.
     */
    private function loadIngredients(ObjectManager $manager): void
    {
        $ingredient = new Ingredient();
        $ingredient->setName('Oeuf')->setCalorie(155)->setUnity(new Unity(Unity::NUMBER))->setConversion(60);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Beurre')->setCalorie(717)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Lait')->setCalorie(46)->setUnity(new Unity(Unity::CLITRE));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Crème fraiche')->setCalorie(300)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Crème liquide')->setCalorie(300)->setUnity(new Unity(Unity::CLITRE));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Mascarpone')->setCalorie(355)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Farine')->setCalorie(355)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Sucre')->setCalorie(400)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Chocolat')->setCalorie(530)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Huile')->setCalorie(864)->setUnity(new Unity(Unity::MLITRE));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Fromage rapée')->setCalorie(376)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Pâte brisée')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Pâte feuilletée')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Pâte sablée')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Pomme de terre')->setCalorie(80)->setUnity(new Unity(Unity::GRAM))->setConversion(150);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Carotte')->setCalorie(40)->setUnity(new Unity(Unity::GRAM))->setConversion(125);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Oignon')->setCalorie(37)->setUnity(new Unity(Unity::GRAM))->setConversion(100);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Echalotte')->setCalorie(63)->setUnity(new Unity(Unity::GRAM))->setConversion(25);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName("Gousse d'Ail")->setCalorie(111)->setUnity(new Unity(Unity::NUMBER))->setConversion(7);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Sel')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Poivre')->setCalorie(0)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Pomme')->setCalorie(54)->setUnity(new Unity(Unity::NUMBER))->setConversion(140);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Poire')->setCalorie(54)->setUnity(new Unity(Unity::NUMBER))->setConversion(120);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Banane')->setCalorie(90)->setUnity(new Unity(Unity::NUMBER))->setConversion(120);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Saucisse de Morteau')->setCalorie(333)->setUnity(new Unity(Unity::NUMBER))->setConversion(400);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Jambon')->setCalorie(134)->setUnity(new Unity(Unity::NUMBER))->setConversion(60);
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Gorgonzola')->setCalorie(379)->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Asperge verte')->setCalorie(24)->setUnity(new Unity(Unity::GRAM))->setConversion(8);
        $manager->persist($ingredient);

        $manager->flush();
    }
}
