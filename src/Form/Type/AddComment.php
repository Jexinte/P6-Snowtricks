<?php
/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  AddComment
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Comment;

/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  AddComment
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

class AddComment extends AbstractType
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
            "content", TextType::class, options: [
            'label' => "Ajouter un commentaire",
            'required' => false,
            "attr" => ["placeholder" => "Exprimez-vous !"]
            ]
        )->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']]);
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
            'data_class' => Comment::class,

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'add_comment',
            ]
        );
    }
}
