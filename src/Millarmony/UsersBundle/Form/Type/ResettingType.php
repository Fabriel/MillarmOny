<?php

namespace Millarmony\UsersBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', RepeatedType::class, array(
            'type'            => PasswordType::class,
            'invalid_message' => 'Les 2 mots de passe doivent être identiques.',
            'options'         => array('required' => true),
            'first_options'   => array('label' => 'Nouveau mot de passe'),
            'second_options'  => array('label' => 'Confirmation du nouveau mot de passe')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'Millarmony\UsersBundle\Entity\User',
            'csrf_token_id' => 'resetting',
            'intention'     => 'resetting'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'millarmony_usersbundle_resetting';
    }

    public function getName()
    {
        return 'millarmony_usersbundle_resetting';
    }

}
