<?php
/**
 * Formulaire d'une recette
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Form;

use App\Entity\Recipe;
use App\Constant\Difficulty;
use App\Constant\Rate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            //->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
