<?php

namespace App\Form\Type;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPassword extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name',TextType::class,options:[
            'label' => 'Utilisateur',
            'required' => false,
            'constraints' => [
                new NotBlank(null,'Ce champ ne peut être vide !'),
            ],
            'attr' => ['placeholder' => 'John']
        ])
            ->add('oldPassword',PasswordType::class,options:[
            'label' => 'Ancien mot de passe',
            'required' => false,
            'constraints' => [
                new NotBlank(null,'Ce champ ne peut être vide !'),
            ],
        ])      ->add('password',PasswordType::class,options:[
            'label' => 'Nouveau mot de passe',
            'required' => false,
            'constraints' => [
                new NotBlank(null,'Ce champ ne peut être vide !'),
                new Regex(
                    pattern: '/^(?=.*[A-Z])(?=.*\d).{8,}$/',
                    message: 'Oops! Le format de votre mot de passe est incorrect, il doit être composé d\'une lettre majuscule , d\'un chiffre et 8 caractères minimum !',
                    match: true,
                )
            ],
        ])

            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => User::class,
            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id'   => 'reset_password',
        ]);
    }
}
