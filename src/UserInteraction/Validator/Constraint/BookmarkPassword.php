<?php


namespace Lenvendo\UserInteraction\Validator\Constraint;


use Symfony\Component\Validator\Constraint;

class BookmarkPassword extends Constraint
{
    public $message = 'Wrong password.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}