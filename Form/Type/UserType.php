<?php

namespace CanalTP\SamEcoreUserManagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use CanalTP\SamEcoreUserManagerBundle\Validator\Constraints\EmailRFC2822;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            'text',
            [
                'label' => 'form.username',
                'attr' => [
                    'class' => 'col-md-4',
                    'placeholder' => 'enter username'
                ],
            ]
        );

        $builder->add(
            'firstname',
            'text',
            [
                'label' => 'form.firstname',
                'attr' => [
                    'class' => 'col-md-4',
                    'placeholder' => 'enter firstname'
                ],
                'constraints' => [
                    new NotBlank(['groups' => 'flow_registration_step1']),
                    new Length(['groups' => 'flow_registration_step1', 'min' => 3, 'max' => 255])
                ]
            ]
        );

        $builder->add(
            'lastname',
            'text',
            [
                'label' => 'form.lastname',
                'attr' => [
                    'class' => 'col-md-4',
                    'placeholder' => 'enter lastname'
                ],
                'constraints' => [
                    new NotBlank(['groups' => 'flow_registration_step1']),
                    new Length(['groups' => 'flow_registration_step1', 'min' => 3, 'max' => 255])
                ]
            ]
        );

        $builder->add(
            'email',
            'text',
            [
                'label' => 'form.email',
                'attr' => [
                    'class' => 'col-md-4',
                    'placeholder' => 'enter email'
                ],
                'constraints' => [
                    new EmailRFC2822(['groups' => 'flow_registration_step1']),
                    new NotBlank(['groups' => 'flow_registration_step1']),
                ]
            ]
        );

        $builder->add(
            'timezone',
            'timezone',
            [
                'label' => 'form.timezone',
                'preferred_choices' => ['Europe/Paris'],
            ]
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'CanalTP\SamEcoreUserManagerBundle\Entity\User',
                'csrf_protection' => false,
                'translation_domain' => 'FOSUserBundle'
            ]
        );
    }

    public function getName()
    {
        return 'create_user';
    }
}
