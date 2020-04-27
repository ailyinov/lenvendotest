<?php


namespace Lenvendo\UserInteraction\Validator\Constraint;


use Symfony\Component\Validator\Constraint;

class BookmarkUniqueUrl extends Constraint
{
    public $message = 'The bookmark for url "{{ string }}" already exists.';
}