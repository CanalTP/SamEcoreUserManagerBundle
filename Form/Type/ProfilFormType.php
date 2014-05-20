<?php

namespace CanalTP\SamEcoreUserManagerBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilFormType extends BaseRegistrationFormType
{
    public function __construct()
    {
        parent::__construct('CanalTP\SamEcoreUserManagerBundle\Entity\User');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add(
            'lastname',
            null,
            array(
                'label' => 'form.lastname',
                'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                    new NotBlank()
                )
            )
        );
        $builder->add(
            'firstname',
            null,
            array(
                'label' => 'form.firstname',
                'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                    new NotBlank()
                )
            )
        );
        $builder->add('email', 'email', array('disabled' => true));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mtt_season';
    }
}
