<?php

namespace App\Form;

use App\Entity\LocalUser;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForgottenPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', Type\EmailType::class)
            ->add('submit', Type\SubmitType::class, [
                'attr' => [
                    'class' => 'btn float-end btn-lg btn-secondary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => LocalUser::class,
        ]);
    }
}
