<?php

namespace Millarmony\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file',       FileType::class)
            ->add('miniature',  MiniaturesType::class)
            ->add('date',       DateType::class , array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'invalid_message' => "Cette date n'est pas valide."
            ))
            ->add('concert',    ChoiceType::class, array(
                'choices'   => array(
                    'Oui'   => true,
                    'Non'   => false
                )
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Millarmony\SiteBundle\Entity\Photos'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'millarmony_sitebundle_photos';
    }

}
