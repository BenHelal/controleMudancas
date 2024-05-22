<?php
// src/Validator/Constraints/RespAndRespEmailNotNull.php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RespAndRespEmailNotNull extends Constraint
{
    public $message = 'The activation field cannot be filled if both resp and resp_email are null.';
}