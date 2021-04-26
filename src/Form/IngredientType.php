<?php
/**
 * Formulaire des ingrédients
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Form;

use App\Entity\Ingredient;
use App\Constant\Unity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'empty_data' => '',
            ])
            ->add('unity', ChoiceType::class, [
                'label' => 'Unité par défaut',
                'choices' => Unity::getChoices(),
                'choice_value' => 'value',
                'choice_label' => 'label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
