<?php


namespace Lenvendo\UserInteraction\Validator\Constraint;


use Symfony\Component\Validator\Constraint;

class BookmarkValidResource extends Constraint
{
    public $message = 'There is no web-page on given URL';
}