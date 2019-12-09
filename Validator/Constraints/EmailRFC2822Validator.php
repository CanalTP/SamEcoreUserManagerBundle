<?php
namespace CanalTP\SamEcoreUserManagerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Swift_Mime_Grammar;

/**
 * @Annotation
 */
class EmailRFC2822Validator extends EmailValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailRFC2822) {
            throw new UnexpectedTypeException($constraint, EmailRFC2822::class);
        }

        parent::validate($value, $constraint);

        $grammar = new Swift_Mime_Grammar();
        if (!preg_match('/^'.$grammar->getDefinition('addr-spec').'$/D', $value)) {
            $this->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(EmailRFC2822::INVALID_FORMAT_ERROR)
                ->addViolation();
        }
    }
}
