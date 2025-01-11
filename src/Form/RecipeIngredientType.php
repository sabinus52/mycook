<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\RecipeIngredient;
use App\Form\DataTransformer\IngredientToNameTransformer;
use App\ValuesList\Unity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
    public function __construct(private readonly IngredientToNameTransformer $transformer)
    {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'autocomplete',
                ],
            ])
            ->add('quantity', IntegerType::class)
            ->add('unity', ChoiceType::class, [
                'choices' => Unity::getChoices(),
                'choice_value' => 'value',
                'choice_label' => 'label',
                'attr' => [
                    'class' => 'unity',
                ],
            ])
            ->add('note', TextType::class)
        ;

        // Transformation de l'objet Ingrédient vers son nom
        $builder->get('ingredient')
            ->addModelTransformer($this->transformer)
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
