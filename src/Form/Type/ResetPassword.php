<?php

/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  Login
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  Login
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class ResetPassword extends AbstractType
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
            'username', TextType::class, options:[
            'label' => 'Utilisateur',
            'required' => false,
            'attr' => ['placeholder' => 'John']
            ]
        )
            ->add(
                'oldPassword', PasswordType::class, options:[
                'label' => 'Ancien mot de passe',
                'required' => false,
                ]
            )      ->add(
                'password', PasswordType::class, options:[
                    'label' => 'Nouveau mot de passe',
                    'required' => false,
                    ]
            )

            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('POST');
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
            'data_class' => User::class,
            'validation_groups' => 'resetPassword',
            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id'   => 'reset_password',
            ]
        );
    }
}
