<?php

/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  UpdateFile
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Form\Type;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Handle form
 *
 * PHP version 8
 *
 * @category Form
 * @package  UpdateFile
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class UpdateFile extends AbstractType
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
            "updatedFile", FileType::class, options: [
            'label' => 'Sélectionner un fichier',
            'required' => false,
            ]
        )
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('PUT');
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
            'data_class' => Media::class,

            'validation_groups' => 'updateTrickFileThatIsNotBanner',

            'csrf_protection' => true,

            'csrf_field_name' => '_token',

            'csrf_token_id' => 'update_media',
            ]
        );
    }

}
