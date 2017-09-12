<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class UserNumeric extends Constraint
{
    public $count;
    public $message = "Число должно содержать {{ string }} знаков.";
}
