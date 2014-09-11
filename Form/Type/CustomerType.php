<?php

namespace CanalTP\SamEcoreUserManagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;

class CustomerType extends AbstractType
{
    private $em;
    private $securityContext;

    public function __construct(EntityManager $entityManager, SecurityContext $securityContext)
    {
        $this->em = $entityManager;
        $this->securityContext = $securityContext;
    }

    private function initCustomerField(FormBuilderInterface $builder)
    {
        $repository = $this->em->getRepository('CanalTPSamCoreBundle:Customer');
        $user = $this->securityContext->getToken()->getUser();
        $isSuperAdmin = $user->hasRole('ROLE_SUPER_ADMIN');
        if ($isSuperAdmin) {
            $choices = $repository->findAllToArray();
        } else {
            $choices = $repository->findByToArray(array(
                'id' => $user->getCustomer()
            ));
        }

        $builder->add('customer', 'choice', array(
            'label' => 'role.field.customer',
            'expanded' => false,
            'choices' => $choices,
            'empty_value' => ($isSuperAdmin ? 'global.please_choose' : false),
            'translation_domain' => 'messages'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->initCustomerField($builder);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'CanalTP\SamEcoreUserManagerBundle\Entity\User',
                'intention'  => 'sam_user',
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'assign_customer';
    }
}
