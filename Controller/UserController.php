<?php

namespace CanalTP\SamEcoreUserManagerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use CanalTP\SamCoreBundle\Event\SamCoreEvents;
use CanalTP\SamCoreBundle\Entity\Application;
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
            return false;
        }
        return true;
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
            $entities = $this->container->get('sam.user_manager')->findUsersBy(['locked' => false]);
        } else {
            $entities = $userManager->findUsersBy(['customer' => $user->getCustomer(), 'locked' => false]);
        }

        $deleteFormViews = [];
        foreach ($entities as $entity) {
            $id                   = $entity->getId();
            $deleteForm           = $this->createDeleteForm($id);
            $deleteFormViews[$id] = $deleteForm->createView();
        }

        return $this->render(
            'CanalTPSamEcoreUserManagerBundle:User:list.html.twig',
            [
                'entities' => $entities,
                'isSuperAdmin' => $isSuperAdmin,
                'delete_forms' => $deleteFormViews,
            ]
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

        return $this->render(
            'CanalTPSamEcoreUserManagerBundle:User:edit.html.twig',
            [
                'id' => ($isNew ? !$isNew : $user->getId()),
                'title' => ($isNew ? 'ctp_user.user.add._title' : 'ctp_user.user.edit._title'),
                'form' => $form->createView(),
                'flow' => $flow,
            ]
        );
    }

    private function isCurrentUser($id)
    {
        if ($this->getUser()->getId() == $id) {
            throw new AccessDeniedException('Seriously, you shouldn\'t delete your account.');
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
            $entity = $userManager->findUserBy(['id' => $id]);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            return $this->render(
                'CanalTPSamEcoreUserManagerBundle:User:delete.html.twig',
                [
                    'entity' => $entity,
                    'delete_form' => $form->createView(),
                ]
            );
        }
        
        $form->bind($request);

        if ($form->isValid() && $this->isCurrentUser($id)) {
            $userManager = $this->container->get('sam.user_manager');
            $entity = $userManager->findUserBy(['id' => $id]);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $this->dispatchEvent($entity, SamCoreEvents::DELETE_USER);
            $userManager->deleteUser($entity);
        }

        return $this->redirect($this->generateUrl('sam_user_list'));
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
        return $this->createFormBuilder(['id' => $id])
            ->add('id', 'hidden')
            ->getForm();
    }

    public function editProfilProcessForm(Request $request, $user)
    {
        $this->get('sam.user_manager')->updateUser($user);
        $request->setLocale($user->getLocale()->getCode());
        $this->get('translator')->setlocale($user->getLocale()->getCode());
        $this->get('session')->set('_locale', $user->getLocale()->getCode());
    }

    /**
     * Displays a form to edit profil of current user.
     */
    public function editProfilAction(Request $request)
    {
        $this->storeHomeUrl($request);

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        $form = $this->createForm(new ProfilFormType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->editProfilProcessForm($request, $user);
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('ctp_user.profil.edit.validate')
            );
        }

        return $this->render('CanalTPSamEcoreUserManagerBundle:User:profil.html.twig', ['form' => $form->createView()]);
    }

    public function toolbarAction()
    {
        $appCanonicalName = $this->get('canal_tp_sam.application.finder')->getCurrentApp()->getCanonicalName();

        return $this->render(
            'CanalTPSamEcoreUserManagerBundle:User:toolbar.html.twig',
            ['currentAppName' => $appCanonicalName]
        );
    }

    private function storeHomeUrl(Request $request)
    {
        if ($request->isMethod('GET')) {
            $url = $this->getHomeUrl($request);
            $this->get('session')->set('home_url', $url);
        }
    }

    private function getHomeUrl(Request $request)
    {
        $app = $this->get('canal_tp_sam.application.finder')->getCurrentApp();
        $path =  $app instanceof Application ? $app->getDefaultRoute() : $request->getBasePath();

        return $request->getScheme() . '://' . $request->getHttpHost() . $path;
    }
}
