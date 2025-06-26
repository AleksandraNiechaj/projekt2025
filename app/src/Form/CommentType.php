<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    EmailType,
    TextareaType,
    SubmitType
};
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nick', TextType::class, [
                'label' => 'Nick',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Treść komentarza',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Dodaj komentarz',
                'attr'  => ['class'=>'btn btn-primary mt-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
