<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Enum\Difficulty;
use App\Enum\Rate;
use Olix\BackOfficeBundle\Form\Type\CollectionType;
use Olix\BackOfficeBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
 *
 * @psalm-suppress MissingTemplateParam
 */
class RecipeType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('person', IntegerType::class, [
                'label' => 'Nombre de personne',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('difficulty', EnumType::class, [
                'label' => 'Niveau de difficulté',
                'class' => Difficulty::class,
                'choice_label' => 'label',
            ])
            ->add('rate', EnumType::class, [
                'label' => 'Coût',
                'class' => Rate::class,
                'choice_label' => 'label',
            ])
            ->add('timePreparation', IntegerType::class, [
                'label' => 'Temps de préparation',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('timeCooking', IntegerType::class, [
                'label' => 'Temps de cuisson',
                'required' => false,
            ])
            ->add('categories', Select2EntityType::class, [
                'label' => 'Catégories',
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
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
                'label' => 'Étapes de la préparation',
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

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
