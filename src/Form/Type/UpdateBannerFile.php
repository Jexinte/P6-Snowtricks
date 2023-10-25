<?php

namespace App\Form\Type;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class UpdateBannerFile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            "updatedBannerFile", FileType::class, options: [
            'label' => 'Sélectionner un fichier',
            'required' => false,
            ]
        )
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('PUT');
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => Media::class,

            'validation_groups' => 'updateBannerFile',

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'update_media',
            ]
        );
    }

}
