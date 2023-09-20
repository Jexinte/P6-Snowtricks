<?php

namespace App\Form\Type;


use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CreateTrick extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("name", TextType::class, options: [
            'label' => 'Nom du trick',
            'required' => false,
            "constraints" => [
                new NotBlank(null, 'Ce champ ne peut être vide !'),
                new Regex(
                    pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
                    message: 'Oops! Le format de votre saisie est incorrect, le nom du trick doit commencer par une lettre majuscule',
                    match: true,
                ),

            ],
            "attr" => ["placeholder" => "Ollie Mollie"],

        ])
            ->add("description", TextareaType::class, options: [
                'label' => 'Description',
                'required' => false,
                "constraints" => [
                    new NotBlank(null, 'Ce champ ne peut être vide !'),
                    new Regex(
                        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
                        message: 'Oops! Le format de votre saisie est incorrect, votre description doit commencer par une lettre majuscule',
                        match: true,
                    ),
                ],

                "attr" => ["placeholder" => "Exprimez-vous!"]
            ])
            ->add("trickGroup", ChoiceType::class, options: [
                'label' => 'Sélectionner un groupe',
                'required' => false,
                'choices' => [
                    'Grabs' => 'Grabs',
                    'Rotations' => 'Rotations',
                    'Flips' => 'Flips',
                    'Rotation désaxée' => 'Rotation désaxée',
                    'Slides' => 'Slides',
                    'One Foot Tricks' => 'One Foot Tricks',
                    'Old School' => 'Old School',
                ],
                "constraints" => [
                    new NotBlank(null, 'Ce champ ne peut être vide !'),
                ]
            ])->add('bannerFile', FileType::class, options: [
                'label' => 'Sélectionner une image qui servivra de bannière pour le trick(obligatoire)',
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
            ->add('images', FileType::class, options: [
                'label' => 'Sélectionner une / plusieurs(s) image(s)',
                'required' => false,
                'constraints' => [
                    new All(
                        new File(
                            maxSize: '3000K',
                            extensions: ['jpg', 'png', 'webp'],
                            extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !',
                        ),
                    )

                ],
                "multiple" => true,
            ])
            ->add('videos', FileType::class, options: [
                'label' => 'Sélectionner une / plusieur(s) vidéo(s)',
                'required' => false,
                'constraints' => [
                    new All(
                        new File(
                            maxSize: '3000K',
                            extensions: ['mp4'],
                            extensionsMessage: 'Seuls les fichiers ayant pour extension mp4 sont acceptés !',
                        ),
                    )
                ],
                "multiple" => true,
            ])
            ->add("embedUrl", TextType::class, options: [
                'label' => 'Url Vidéo Dailymotion / Youtube',
                'required' => false,
                "constraints" => [
                    new Regex(
                        pattern: '/<iframe[^>]+src="([^"]+)"/i',
                        message: "Oops ! Il semblerait que le format de votre url n'est pas bon, merci de vérifier ce qu'il en est",
                        match: true,
                    ),
                ],
                "attr" => ["placeholder" => "Insérer ici l'adresse url de votre vidéo"]
            ])
            ->setAction('post')
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'constraints' => [
                new UniqueEntity('name', 'Le nom du trick est indisponible'),
            ],
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'csrf_token_id' => 'create_trick',
        ]);
    }


}
