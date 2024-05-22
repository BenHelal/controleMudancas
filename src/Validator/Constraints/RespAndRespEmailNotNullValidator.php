<?php 

// src/Validator/Constraints/RespAndRespEmailNotNullValidator.php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\FormInterface;
use App\Entity\Client;

class RespAndRespEmailNotNullValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Constraints\RespAndRespEmailNotNull */

        if (null === $value || '' === $value) {
            return;
        }

        $form = $this->context->getObject();
        if (!$form instanceof FormInterface) {
            return;
        }

        $data = $form->getData();
        
        if (!$data instanceof Client) {
            return;
        }

        $resp = $data->getResp();
        $respEmail = $data->getRespEmail();

        if (null === $resp && null === $respEmail && null !== $value) {
            $this->context->buildViolation($constraint->message)
                ->atPath('activation')
                ->addViolation();
        }
    }
}