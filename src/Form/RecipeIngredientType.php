<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Enum\Unity;
use Olix\BackOfficeBundle\Form\Type\Select2AjaxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de la liste des ingrédients de la recette.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * @psalm-suppress MissingTemplateParam
 */
class RecipeIngredientType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', Select2AjaxType::class, [
                'class' => Ingredient::class,
                'class_label' => 'name',
                'class_property' => 'name',
                'allow_add' => true,
                'ajax' => [
                    'route' => 'ingredient_autocomplete_select2',
                ],
                'required' => false,
            ])
            ->add('quantity', IntegerType::class, [
                'required' => false,
            ]
            )
            ->add('unity', EnumType::class, [
                'class' => Unity::class,
                'choice_label' => 'label',
                'attr' => [
                    'class' => 'unity',
                ],
            ])
            ->add('note', TextType::class, [
                'required' => false,
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
            'block_prefix' => 'recipe_ingredient', // Custom form => recipe_ingredient_row
        ]);
    }
}
