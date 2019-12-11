<?php
namespace CanalTP\SamEcoreUserManagerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class EmailRFC2822 extends Email
{
    public $message = 'form.error.email_rfc2822';
}
