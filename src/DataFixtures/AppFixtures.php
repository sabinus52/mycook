<?php
/**
 * Données initiales de l'applicaton
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Constant\Unity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $this->loadCategories($manager);
        $this->loadIngredients($manager);
    }


    /**
     * Chargement des catégories
     */
    private function loadCategories(ObjectManager $manager)
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
     * Chargement des ingrédients
     */
    private function loadIngredients(ObjectManager $manager)
    {
        $ingredient = new Ingredient();
        $ingredient->setName('Oeuf')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Beurre')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Lait')->setUnity(new Unity(Unity::CLITRE));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Crème fraiche')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Crème liquide')->setUnity(new Unity(Unity::CLITRE));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Farine')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Sucre')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Chocolat')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Huile')->setUnity(new Unity(Unity::CLITRE));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Fromage rapée')->setUnity(new Unity(Unity::GRAM));
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
        $ingredient->setName('Pomme de terre')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Carotte')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Oignon')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Echalotte')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Gousse d\'Ail')->setUnity(new Unity(Unity::GRAM));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Sel')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Poivre')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Pomme')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Poire')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $ingredient = new Ingredient();
        $ingredient->setName('Banane')->setUnity(new Unity(Unity::NUMBER));
        $manager->persist($ingredient);

        $manager->flush();
    }

}