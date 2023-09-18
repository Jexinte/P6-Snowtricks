<?php

namespace App\Form\Type;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
class Login extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("name", TextType::class, options: [
            'label' => 'Utilisateur',
            'required' => false,
            "constraints" => [
                new NotBlank(null, 'Ce champ ne peut être vide !'),
                new Regex(
                    pattern: '/^(?=[A-Z])([A-Za-z0-9]{1,10})$/',
                    message: 'Le nom d\'utilisateur doit commencer par une majuscule , ne peut contenir que des chiffres et ne doit excéder 10 caractères !',
                    match: true
                )
            ]
        ])
            ->add('password', PasswordType::class, options: [
                'label' => 'Mot de passe',
                'required' => false,
                'constraints' => [
                    new NotBlank(null, 'Ce champ ne peut être vide !'),
                ],

            ]
            )->setAction('post')

            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']]);

    }
        public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => User::class,

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id'   => 'login',
        ]);
    }
    }
