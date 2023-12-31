<?php

/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  CreateTrick
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Form\Type;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  CreateTrick
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

class CreateTrick extends AbstractType
{
    /**
     * Summary of buildForm
     *
     * @param FormBuilderInterface $builder Object
     * @param array                $options array
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            "name", TextType::class, options: [
            'label' => 'Nom du trick',
            'required' => false,
            "attr" => ["placeholder" => "Ollie Mollie"],
            ]
        )
            ->add(
                "description", TextareaType::class, options: [
                'label' => 'Description',
                'required' => false,
                "attr" => ["placeholder" => "Exprimez-vous!"]
                ]
            )
            ->add(
                "trickGroup", ChoiceType::class, options: [
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

                ]
            );
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
            'csrf_field_name' => 'token',
            'csrf_token_id' => 'create_trick_text',
            ]
        );
    }


}
