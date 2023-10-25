<?php
/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  UpdateTrickContent
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Form\Type;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  UpdateTrickContent
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class UpdateTrickContent extends AbstractType
{
    /**
     * Summary of buildForm
     *
     * @param FormBuilderInterface $builder Object
     * @param array                $options array
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "name", TextType::class, options: [
            'label' => 'Nom du trick',
            'required' => false,
            ]
        )
            ->add(
                'description', TextareaType::class, options: [
                'label' => 'Description',
                'required' => false,
                ]
            )
            ->add(
                'trickGroup', ChoiceType::class, options: [
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
                ]
            )->add(
                'save',
                SubmitType::class,
                ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']]
            )->setMethod("PUT");
    }

    /**
     * Summary of configureOptions
     *
     * @param OptionsResolver $resolver Object
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => Trick::class,

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'update_trick_content',
            ]
        );
    }

}
