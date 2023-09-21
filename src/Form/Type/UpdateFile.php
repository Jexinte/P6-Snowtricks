<?php

namespace App\Form\Type;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class UpdateFile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("updatedFile", FileType::class, options: [
            'label' => 'Sélectionner un fichier',
            'required' => false,
            'constraints' => [
                new File(
                    maxSize: '3000K',
                    extensions: ['jpg', 'png', 'webp', 'mp4'],
                    extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png ,webp et mp4 sont acceptés !',
                ),
            ]
        ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('PUT');
    }
        public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'update_media',
        ]);
    }

    }
