<?php

namespace CanalTP\SamEcoreUserManagerBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use CanalTP\NmmPortalBundle\Entity\Language;

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

        $builder->add(
            'timezone',
            'timezone',
            [
              'label' => 'form.timezone',
              'preferred_choices' => array('Europe/Paris'),
              'translation_domain' => 'FOSUserBundle'
            ]
        );

        $defaultLanguage = null;
        if (isset($options['attr']['default_language'])) {
            $defaultLanguage = $options['attr']['default_language'];
        }

        $builder->add(
            'language',
            EntityType::class,
            [
                'class' => Language::class,
                'choice_label' => 'label',
                'label' => 'customer.language',
                'data' => $defaultLanguage
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'edit_user_profil';
    }
}
