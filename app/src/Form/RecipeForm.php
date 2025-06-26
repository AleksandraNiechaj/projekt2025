<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('categories', EntityType::class, [
                'class'        => Category::class,
                'choice_label' => 'name',           // pokazujemy pole name, nie id
                'multiple'     => true,             // można wybrać wiele
                'expanded'     => false,            // select box
                'required'     => false,            // nieobowiązkowe
                'placeholder'  => 'Wybierz kategorię',
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
