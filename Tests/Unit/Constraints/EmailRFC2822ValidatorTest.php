<?php

namespace CanalTP\SamEcoreUserManagerBundle\Tests\Unit\Constraints;

use CanalTP\SamEcoreUserManagerBundle\Validator\Constraints\EmailRFC2822;
use CanalTP\SamEcoreUserManagerBundle\Validator\Constraints\EmailRFC2822Validator;

class EmailRFC2822ValidatorTest extends ValidatorTestCase
{
    /**
     * @var EmailRFC2822Validator
     */
    private $validator;

    /**
     * @var EmailRFC2822
     */
    private $constraint;

    protected function setUp()
    {
        parent::setUp();
        $this->constraint = new EmailRFC2822();
        $this->validator = new EmailRFC2822Validator();
    }

    protected function getValidatorInstance()
    {
        return $this->validator;
    }

    public function testValidationOK()
    {
        $validator = $this->initValidator();

        $validator->validate('email@mail.com', $this->constraint);
        $validator->validate('email@local.host', $this->constraint);
    }

    public function testValidationKO()
    {
        $validator = $this->initValidator($this->constraint->message);
        $validator->validate('émail@local.host', $this->constraint);
    }
}
