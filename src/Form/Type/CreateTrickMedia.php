<?php

namespace App\Form\Type;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTrickMedia extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'bannerFile', FileType::class, options: [
                'label' => 'Sélectionner une image qui servivra de bannière pour le trick(obligatoire)',
                'required' => false,

                ]
            )
            ->add(
                'images', FileType::class, options: [
                'label' => 'Sélectionner une / plusieurs(s) image(s)',
                'required' => false,
                "multiple" => true,
                ]
            )
            ->add(
                'videos', FileType::class, options: [
                'label' => 'Sélectionner une / plusieur(s) vidéo(s)',
                'required' => false,
                "multiple" => true,
                ]
            )
            ->add(
                "embedUrl", TextType::class, options: [
                'label' => 'Url Vidéo Dailymotion / Youtube',
                'required' => false,
                "attr" => ["placeholder" => "Insérer ici l'adresse url de votre vidéo"]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => Media::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'csrf_token_id' => 'create_trick_media',
            ]
        );
    }
}
