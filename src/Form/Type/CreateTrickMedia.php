<?php

namespace App\Form\Type;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CreateTrickMedia extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('bannerFile', FileType::class, options: [
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'csrf_token_id' => 'create_trick_media',
        ]);
    }
}
