<?php

namespace CanalTP\SamEcoreUserManagerBundle\Migrations\pdo_pgsql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

/**
 * Adding created_at column in public.t_user_usr table
 */
class Version001 extends AbstractMigration
{
    const VERSION = '0.0.1';

    public function getName()
    {
        return self::VERSION;
    }

    /**
     * Adding timezone column into user table
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = 'ALTER TABLE public.t_user_usr ';
        $sql.= 'ADD COLUMN usr_created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone';

        $this->addSql($sql);
    }

    /**
     * Drop timezone column from user table
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE public.t_user_usr DROP COLUMN usr_created_at');
    }
}
