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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class SignUp extends AbstractType
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
            ->add("file", FileType::class, options: [
                'label' => 'Image de profil',
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '3000K',
                        extensions: ['jpg', 'png', 'webp'],
                        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !',
                    ),
                    new NotBlank(message: 'Veuillez sélectionner un fichier !')
                ]
            ])
            ->add('email', EmailType::class, options: [
                'label' => 'Email',
                'required' => false,
                'constraints' => [
                    new NotBlank(null, 'Ce champ ne peut être vide !'),
                    new Regex(
                        pattern: '/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/',
                        message: 'Oops! Le format de votre saisie est incorrect,merci de suivre le format requis : nomadressemail@domaine.extension',
                        match: true,
                    )
                ]
            ])
            ->add('password', PasswordType::class, options: [
                'label' => 'Mot de passe',
                'required' => false,
                'constraints' => [
                    new NotBlank(null, 'Ce champ ne peut être vide !'),
                    new Regex(
                        pattern: '/^(?=.*[A-Z])(?=.*\d).{8,}$/',
                        message: 'Oops! Le format de votre mot de passe est incorrect, il doit être composé d\'une lettre majuscule , d\'un chiffre et 8 caractères minimum !',
                        match: true,
                    )
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setAction("/signup/registration")->setMethod('POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'sign_up',
        ]);
    }
}

