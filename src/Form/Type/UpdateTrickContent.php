<?php

namespace App\Form\Type;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class UpdateTrickContent extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("name", TextType::class, options: [
            'label' => 'Nom du trick',
            'required' => false,
        ])
            ->add('description', TextareaType::class, options: [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('trickGroup', ChoiceType::class, options: [
                'label' => 'Sélectionner un groupe',
                "choices" => [
                    "Grabs" => "Grabs",
                    "Rotations" => "Rotations",
                    "Flips" => "Flips",
                    "Rotation désaxées" => "Rotation désaxées",
                    "Slides" => "Slides",
                    "One Foot Tricks" => "One Foot Tricks",
                    "Old School" => "Old School"
                ]
            ])->add(
                'save',
                SubmitType::class,
                ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']]
            )->setMethod("PUT");
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'update_trick_content',
        ]);
    }

}
