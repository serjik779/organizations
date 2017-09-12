<?php
/**
 * Created by PhpStorm.
 * User: sergey.kalchenko
 * Date: 11.09.2017
 * Time: 17:02
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserNumericValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $count = $constraint->count;
        if (strlen($value) != $count) {
            $constraint->message = str_replace('{{ string }}', $count, $constraint->message);
            if (!preg_match('/^\d+$/', $value)) {
                $constraint->message = 'В данном поле должны быть только цифры';
            }
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}