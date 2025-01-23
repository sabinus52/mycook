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
use App\ValuesList\Unity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire des ingrédients.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * @psalm-suppress MissingTemplateParam
 */
class IngredientType extends AbstractType
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
            ->add('calorie', IntegerType::class, [
                'label' => 'Calories pour 100 g',
                'required' => false,
            ])
            ->add('unity', ChoiceType::class, [
                'label' => 'Unité par défaut',
                'choices' => Unity::getChoices(),
                'choice_value' => 'value',
                'choice_label' => 'label',
                'required' => false,
            ])
            ->add('conversion', NumberType::class, [
                'label' => 'Poids en gramme pour 1 ingrédient',
                'html5' => true,
                'required' => false,
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
