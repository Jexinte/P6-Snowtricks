<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ForgotPassword extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'username', TextType::class, options:[
            'label' => 'Utilisateur',
            'required' => false,

            'attr' => ['placeholder' => 'John']
            ]
        )->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => User::class,
            'validation_groups' => 'forgotPassword',

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id'   => 'reset_password_with_link',
            ]
        );
    }
}
