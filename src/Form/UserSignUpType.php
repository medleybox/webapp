<?php

namespace App\Form;

use App\Entity\LocalUser;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email', Type\EmailType::class)
            ->add('password', Type\PasswordType::class)
            ->add('submit', Type\SubmitType::class, [
                'attr' => [
                    'class' => 'btn float-right btn-lg btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LocalUser::class,
        ]);
    }
}
