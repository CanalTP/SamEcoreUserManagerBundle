<?php

namespace CanalTP\SamEcoreUserManagerBundle\Entity;

use CanalTP\SamCoreBundle\Entity\Role;
use FOS\UserBundle\Model\User as AbstractUser;
use Doctrine\Common\Collections\ArrayCollection;

class User extends AbstractUser
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const STATUS_STEP_1 = 1;
    const STATUS_STEP_2 = 2;
    const STATUS_STEP_3 = 3;
    const MAIL_SENDED = 4;

    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * The salt to use for hashing
     *
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * @var integer
     */
    protected $status = 1;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * @var boolean
     */
    protected $expired;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var array
     */
    protected $role;

    /**
     * @var boolean
     */
    protected $credentialsExpired;

    /**
     * @var \DateTime
     */
    protected $credentialsExpireAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $roleGroupByApplications;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $userRoles;

    protected $applications;

    protected $customer;

    /**
     * User creation date
     *
     * @var \DateTime
     */
    protected $createdAt;

    protected $language;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->userRoles = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    /**
     * Set firstName
     *
     * @param  string $firstName
     * @return User
     */
    public function setFirstname($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param  string $lastName
     * @return User
     */
    public function setLastname($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastName;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add roles
     *
     * @param Role $role
     * @return User
     */
    public function addUserRole(Role $role)
    {
        $this->userRoles[] = $role;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param Role $role
     */
    // public function removeRole(Role $role)
    // {
    //     $this->roles->removeElement($role);
    // }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set currentApplicationRoles
     *
     * @return Role
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;

        return $this;
    }


    /**
     * Add roles
     *
     * @param Role $role
     * @return User
     */
    public function addApplication($application)
    {
        $this->applications[] = $application;

        return $this;
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * Set currentApplicationRoles
     *
     * @return Role
     */
    public function setUserRoles($roles)
    {
        $this->userRoles = $roles;

        return $this;
    }

    /**
     * Remove userRole
     *
     * @param \CanalTP\SamCoreBundle\Entity\Role $userRole
     */
    public function removeUserRole(\CanalTP\SamCoreBundle\Entity\Role $userRole)
    {
        $this->userRoles->removeElement($userRole);
    }

    /**
     * Add roleGroupByApplication
     *
     * @param Role $roleParent
     * @return Role
     */
    public function addRoleGroupByApplication(Role $roleGroupByApplication)
    {
        $this->roleGroupByApplications[] = $roleGroupByApplication;

        return $this;
    }

    /**
     * Remove roleGroupByApplication
     *
     * @param \CanalTP\SamCoreBundle\Entity\Application $roleParent
     */
    public function removeRoleGroupByApplication(Role $roleGroupByApplication)
    {
        $this->roleGroupByApplications->removeElement($roleGroupByApplication);
    }

    /**
     * Get roleGroupByApplications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoleGroupByApplications()
    {
        return $this->roleGroupByApplications;
    }

    public function getRoles()
    {
        $roles = array();
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;


        foreach ($this->getUserRoles() as $role) {
            $roles[$role->getId()] = $role->getCanonicalName();
        }
        return array_unique($roles);
    }

    public function onPostLoad()
    {
        $aRoles = array();

        foreach ($this->getRoles() as $role) {
            $aRoles[] = $role->getCanonicalName();
        }
        $this->setRoles($aRoles);
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function hasRole($role)
    {
        return (in_array($role, $this->getRoles()));
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function getStatusKey()
    {
        if ($this->isEnabled()) {
            return ('ctp_user.user.status.active');
        }
        return ('ctp_user.user.status.step_' . $this->getStatus());
    }

    /**
     * Set timezone
     *
     * @param  string $timezone
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Get credentialsExpired
     *
     * @return boolean
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Get credentialsExpireAt
     *
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets created at attribute at now
     */
    public function setCretedNow()
    {
        $this->setCreatedAt(new \DateTime('now'));

        return $this;
    }

    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function getLocale()
    {
        return $this->language ? $this->language : $this->customer->getLanguage();
    }
}
