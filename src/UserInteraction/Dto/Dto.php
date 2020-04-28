<?php


namespace Lenvendo\UserInteraction\Dto;


use Symfony\Component\HttpFoundation\Request;

interface Dto
{
    public static function createFromRequest(Request $request);
}