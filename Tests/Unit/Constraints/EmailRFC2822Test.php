<?php

namespace CanalTP\SamEcoreUserManagerBundle\Tests\Unit\Constraints;

use CanalTP\SamEcoreUserManagerBundle\Tests\Unit\UnitTestCase;
use CanalTP\SamEcoreUserManagerBundle\Validator\Constraints\EmailRFC2822;

class EmailRFC2822Test extends UnitTestCase
{
    /**
     * @var EmailRFC2822
     */
    private $constraint;

    protected function setUp()
    {
        parent::setUp();
        $this->constraint = new EmailRFC2822();
    }

    public function testConstraintClass()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraints\Email', $this->constraint);
    }

    public function testMessage()
    {
        $this->assertEquals('This email address does not comply with RFC 2822', $this->constraint->message);
    }
}
