<?php

namespace CanalTP\SamEcoreUserManagerBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getIncativeUsersSince(\DateTime $lastLoginDate)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.lastLogin < :lastLoginDate')
            ->orWhere('u.lastLogin IS NULL')
            ->setParameter('lastLoginDate', $lastLoginDate);

        return $qb->getQuery()->getResult();
    }
}
