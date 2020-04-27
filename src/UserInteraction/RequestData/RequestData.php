<?php


namespace Lenvendo\UserInteraction\RequestData;


use Symfony\Component\HttpFoundation\Request;

interface RequestData
{
    public static function createFromRequest(Request $request);
}