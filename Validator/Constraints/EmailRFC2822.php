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
    public $message = 'This email address does not comply with RFC 2822';
}
