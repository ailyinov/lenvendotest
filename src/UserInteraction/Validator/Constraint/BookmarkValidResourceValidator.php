<?php

namespace Lenvendo\UserInteraction\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BookmarkValidResourceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('HEAD', $value);

        $responseCode = $response->getStatusCode();
        $contentType = $response->getHeaderLine('content-type');

        if ($responseCode !== 200 || strpos($contentType, 'text/html') === false) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}