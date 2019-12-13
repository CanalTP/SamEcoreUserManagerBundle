<?php

namespace CanalTP\SamEcoreUserManagerBundle\Tests\Unit\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;
use CanalTP\SamEcoreUserManagerBundle\Tests\Unit\UnitTestCase;

abstract class ValidatorTestCase extends UnitTestCase
{
    /**
     * Initialise the validator.
     *
     * @param string|null $expectedMessage
     *
     * @return ConstraintValidator
     */
    protected function initValidator($expectedMessage = null)
    {
        $builder = $this->getMockBuilder(ConstraintViolationBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addViolation'])
            ->getMock();

        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildViolation'])
            ->getMock();

        if ($expectedMessage) {
            $builder->expects($this->once())->method('addViolation');
            $context
                ->expects($this->once())
                ->method('buildViolation')
                ->with($this->equalTo($expectedMessage))
                ->willReturn($builder);
        } else {
            $context->expects($this->never())->method('buildViolation');
        }

        $validator = $this->getValidatorInstance();
        $validator->initialize($context);

        return $validator;
    }

    /**
     * @return ConstraintValidator
     */
    abstract protected function getValidatorInstance();
}
