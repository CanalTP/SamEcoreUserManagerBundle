<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CanalTP\SamEcoreUserManagerBundle\Controller;

use CanalTP\SamCoreBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends AbstractController
{
    public function loginAction()
    {
        /**
         * @var $request \Symfony\Component\HttpFoundation\Request
         */
        $request = $this->container->get('request');

        /**
         * @var $session \Symfony\Component\HttpFoundation\Session
         */
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk(http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->get('security.csrf.token_manager')->refreshToken('authenticate');

        if (true === $this->container->get('security.context')->isGranted('ROLE_USER')) {
            $handler = $this->container->get('sam.component.authentication.handler.login_success_handler');
            return ($handler->onAuthenticationSuccess($request, $this->container->get('security.context')->getToken()));
        }

        return $this->renderLogin(
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token'    => $csrfToken,
            )
        );
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        $template = sprintf(
            'FOSUserBundle:Security:login.html.%s',
            $this->container->getParameter('fos_user.template.engine')
        );

        return $this->container->get('templating')->renderResponse($template, $data);
    }

    public function checkAction()
    {
        throw new \RuntimeException(
            implode(
                ' ',
                array(
                    'You must configure the check path to be handled',
                    'by the firewall using form_login in',
                    'your security firewall configuration.',
                )
            )
        );
    }

    public function logoutAction()
    {
        throw new \RuntimeException(
            'You must activate the logout in your security firewall configuration.'
        );
    }
}
