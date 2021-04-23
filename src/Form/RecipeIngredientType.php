<?php
/**
 * Formulaire de la liste des ingrédients de la recette
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Form;

use App\Constant\Unity;
use App\Entity\RecipeIngredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ingredient', EntityType::class, [
                'class' => 'App:Ingredient',
                'choice_label' => 'name',
            ])
            ->add('quantity', IntegerType::class)
            ->add('unity', ChoiceType::class, [
                'choices' => Unity::getChoices(),
                'choice_value' => 'value',
                'choice_label' => 'label',
            ])
            ->add('note', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
            'block_prefix' => 'recipe_ingredient', // Custom form => recipe_ingredient_row
        ]);
    }
}