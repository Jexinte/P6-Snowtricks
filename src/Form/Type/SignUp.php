<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class SignUp extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("username", TextType::class, options: [
            'label' => 'Utilisateur',
            'required' => false,
        ]
        )
            ->add("file", FileType::class, options: [
                'label' => 'Image de profil',
                'required' => false,

            ])
            ->add('email', EmailType::class, options: [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('password', PasswordType::class, options: [
                'label' => 'Mot de passe',
                'required' => false,

            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setAction("/signup/registration")->setMethod('POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,

            'validation_groups' => 'signUp',

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'sign_up',
        ]);
    }
}

