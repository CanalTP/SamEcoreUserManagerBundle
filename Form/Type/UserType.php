<?php

namespace CanalTP\SamEcoreUserManagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            'text',
            array(
                'label' => 'form.username',
                'attr' => array(
                    'class' => 'col-md-4',
                    'placeholder' => 'enter username'
                ),
                'translation_domain' => 'FOSUserBundle'
            )
        );

        $builder->add(
            'firstname',
            'text',
            array(
                'label' => 'form.firstname',
                'attr' => array(
                    'class' => 'col-md-4',
                    'placeholder' => 'enter firstname'
                ),
                'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                        new NotBlank(array('groups' => 'flow_registration_step1')),
                        new Length(array('groups' => 'flow_registration_step1', 'min' => 3, 'max' => 255))
                )
            )
        );

        $builder->add(
            'lastname',
            'text',
            array(
                'label' => 'form.lastname',
                'attr' => array(
                    'class' => 'col-md-4',
                    'placeholder' => 'enter lastname'
                ),
                'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                        new NotBlank(array('groups' => 'flow_registration_step1')),
                        new Length(array('groups' => 'flow_registration_step1', 'min' => 3, 'max' => 255))
                )
            )
        );

        $builder->add(
            'email',
            'text',
            array(
                'label' => 'form.email',
                'attr' => array(
                    'class' => 'col-md-4',
                    'placeholder' => 'enter email'
                ),
                'translation_domain' => 'FOSUserBundle'
            )
        );

        $builder->add(
            'timezone',
            'timezone',
            [
              'label' => 'form.timezone',
              'preferred_choices' => array('Europe/Paris'),
              'translation_domain' => 'FOSUserBundle'
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'CanalTP\SamEcoreUserManagerBundle\Entity\User',
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'create_user';
    }
}
