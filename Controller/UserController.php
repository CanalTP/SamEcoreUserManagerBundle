<?php

namespace CanalTP\SamEcoreUserManagerBundle\Controller;

use CanalTP\SamCoreBundle\Event\SamCoreEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use CanalTP\SamCoreBundle\Controller\AbstractController;
use CanalTP\SamEcoreUserManagerBundle\Form\Type\ProfilFormType;
use CanalTP\SamEcoreUserManagerBundle\Entity\User;
use CanalTP\SamCoreBundle\Exception\UserEventException;
use CanalTP\SamEcoreUserManagerBundle\Event\UserEvent;

class UserController extends AbstractController
{
    private function dispatchEvent(UserInterface $user, $type)
    {
        $event = new UserEvent($user);
        try {
            $this->get('event_dispatcher')->dispatch($type, $event);
        } catch (UserEventException $e) {
            $this->addFlashMessage('danger', $e->getMessage());
            return (false);
        }
        return (true);
    }

    /**
     * Lists all User entities.
     */
    public function listAction()
    {
        $this->isAllowed('BUSINESS_VIEW_USER');

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $this->get('security.context')->getToken()->getUser();
        $isSuperAdmin = $user->hasRole('ROLE_SUPER_ADMIN');
        if ($isSuperAdmin) {
            $entities = $this->container->get('sam.user_manager')->findUsersBy(array('locked' => false));
        } else {
            $entities = $userManager->findUsersBy(array('customer' => $user->getCustomer(), 'locked' => false));
        }

        $deleteFormViews = array();
        foreach ($entities as $entity) {
            $id                   = $entity->getId();
            $deleteForm           = $this->createDeleteForm($id);
            $deleteFormViews[$id] = $deleteForm->createView();
        }

        return $this->render(
            'CanalTPSamEcoreUserManagerBundle:User:list.html.twig',
            array(
                'entities'     => $entities,
                'isSuperAdmin' => $isSuperAdmin,
                'delete_forms' => $deleteFormViews,
            )
        );
    }

    public function editAction(Request $request, User $user = null)
    {
        $this->isGranted('BUSINESS_MANAGE_USER');
        $isNew = ($user == null);
        $flow = $this->get('sam.registration.form.flow');
        $flow->bind(($isNew ? new User() : $user));
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $user = $form->getData();

            $flow->saveCurrentStepData($form);
            $user->setStatus($flow->getCurrentStep());
            $this->get('fos_user.registration.form.handler')->save(
                $user,
                false
            );
            $isNew = false;
            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $flow->reset();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'profile.flash.updated'
                );

                return $this->redirect($this->generateUrl('sam_user_list'));
            }
        }

        return $this->render('CanalTPSamEcoreUserManagerBundle:User:edit.html.twig', array(
            'id' => ($isNew ? !$isNew : $user->getId()),
            'title' => ($isNew ? 'ctp_user.user.add._title' : 'ctp_user.user.edit._title'),
            'form' => $form->createView(),
            'flow' => $flow
        ));
    }

    private function isCurrentUser($id)
    {
        if ($this->getUser()->getId() == $id) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Seriously, you shouldn\'t delete your account.');
        }
        return true;
    }

    /**
     * Deletes a User entity.
     */
    public function deleteAction(Request $request, $id)
    {
        $this->isAllowed('BUSINESS_MANAGE_USER');

        $form = $this->createDeleteForm($id);

        if ($request->getMethod() == 'GET') {
            $userManager = $this->container->get('fos_user.user_manager');
            $entity = $userManager->findUserBy(array('id' => $id));

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            return $this->render(
                'CanalTPSamEcoreUserManagerBundle:User:delete.html.twig',
                array(
                    'entity'      => $entity,
                    'delete_form' => $form->createView(),
                )
            );
        } else {
            $form->bind($request);

            if ($form->isValid() && $this->isCurrentUser($id)) {
                $userManager = $this->container->get('sam.user_manager');
                $entity = $userManager->findUserBy(array('id' => $id));

                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find User entity.');
                }

                $this->dispatchEvent($entity, SamCoreEvents::DELETE_USER);
                $userManager->deleteUser($entity);
            }

            return $this->redirect($this->generateUrl('sam_user_list'));
        }
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }

    public function editProfilProcessForm($user)
    {
        $this->get('sam.user_manager')->updateUser($user);
        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('ctp_user.profil.edit.validate')
        );
    }

    /**
     * Displays a form to edit profil of current user.
     */
    public function editProfilAction(Request $request)
    {
        //to translate the flashbag to chosen language
        $request = $this->getRequest();
        $postData = $request->request->get('edit_user_profil');
        if ($postData && $request->getLocale() !== $postData['language']) {
            $request->setLocale($postData['language']);
            $this->editProfilAction();
        }
        $app = $this->get('canal_tp_sam.application.finder')->getCurrentApp();
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id' => $id));
        $options['attr']['selected_language'] = $user->getLocale();
        $form = $this->createForm(
            new ProfilFormType(),
            $user,
            $options
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->editProfilProcessForm($user);
            $this->get('session')->set('_locale', $user->getLocale()->getCode());
        }
        return $this->render(
            'CanalTPSamEcoreUserManagerBundle:User:profil.html.twig',
            array(
                'form' => $form->createView(),
                'defaultAppHomeUrl' => $app->getDefaultRoute()
            )
        );
    }

    public function toolbarAction()
    {
        $appCanonicalName = $this->get('canal_tp_sam.application.finder')->getCurrentApp()->getCanonicalName();

        return $this->render(
            'CanalTPSamEcoreUserManagerBundle:User:toolbar.html.twig',
            array('currentAppName' => $appCanonicalName)
        );
    }
}
