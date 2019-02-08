<?php

namespace CanalTP\SamEcoreUserManagerBundle\Security;

use Symfony\Component\Security\Core\User\UserChecker as SymfonyUserChecker;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker extends SymfonyUserChecker
{

    /**
     * {@inheritDoc}
     */
    public function checkPreAuth(UserInterface $user)
    {
        try {
            parent::checkPreAuth($user);
        }
        catch (LockedException $e) {
            $ex = new LockedException('security.login.locked');
            $ex->setUser($user);
            throw $ex;
        }
        catch (DisabledException $e) {
            $ex = new DisabledException('security.login.disabled');
            $ex->setUser($user);
            throw $ex;
        }
        catch (AccountExpiredException $e) {
            $ex = new AccountExpiredException('security.login.accountExpired');
            $ex->setUser($user);
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkPostAuth(UserInterface $user)
    {
        try {
            parent::checkPostAuth($user);
        } catch (CredentialsExpiredException $e) {
            $ex = new CredentialsExpiredException('security.login.credentialExpired');
            $ex->setUser($user);
            throw $ex;
        }
    }
}
