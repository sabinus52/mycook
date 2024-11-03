<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Form;

use App\Constant\Difficulty;
use App\Constant\Rate;
use App\Entity\Category;
use App\Entity\Recipe;
use Olix\BackOfficeBundle\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Formulaire d'une recette.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'empty_data' => '',
            ])
            ->add('person', IntegerType::class, [
                'label' => 'Nombre de personne',
                'empty_data' => '',
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Niveau de difficulté',
                'choices' => Difficulty::getChoices(),
                'choice_value' => 'value',
                'choice_label' => 'label',
                'empty_data' => '',
            ])
            ->add('rate', ChoiceType::class, [
                'label' => 'Coût',
                'choices' => Rate::getChoices(),
                'choice_value' => 'value',
                'choice_label' => 'label',
                'empty_data' => '',
            ])
            ->add('timePreparation', IntegerType::class, [
                'label' => 'Temps de préparation',
                'empty_data' => '',
            ])
            ->add('timeCooking', IntegerType::class, [
                'label' => 'Temps de cuisson',
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégories',
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo du plat',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Uniquement des photos au format jpg ou png',
                    ]),
                ],
            ])
            ->add('ingredients', CollectionType::class, [
                'label' => 'Liste des ingrédients',
                'button_label_add' => 'Nouvel ingrédient',
                'entry_type' => RecipeIngredientType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'block_name' => 'recipe_ingredients', // Custom form => _recipe_recipe_ingredients_row
                'attr' => [
                    'class' => 'collection-widget',
                ],
            ])
            ->add('steps', CollectionType::class, [
                'label' => 'Etapes de la préparation',
                'button_label_add' => 'Nouvelle étape',
                'entry_type' => StepType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'block_name' => 'recipe_steps', // Custom form => _recipe_recipe_steps_row
                'attr' => [
                    'class' => 'collection-widget',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
