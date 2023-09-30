<?php

namespace App\Form\Type;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class UpdateEmbedUrl extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("embedUrl", TextType::class, options: [
            'label' => 'Url Vidéo Dailymotion / Youtube',
            'required' => false,

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
