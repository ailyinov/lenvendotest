<?php

namespace Lenvendo\UserInteraction\Validator\Constraint;

use Lenvendo\UserInteraction\Dto\AddBookmarkDto;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BookmarkValidResourceValidator extends ConstraintValidator
{
    /**
     * @param AddBookmarkDto $addBookmarkDto
     * @param Constraint $constraint
     */
    public function validate($addBookmarkDto, Constraint $constraint)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($addBookmarkDto->getUrl());

        $responseCode = $response->getStatusCode();
        $contentType = $response->getHeaderLine('content-type');

        if ($responseCode !== 200 || strpos($contentType, 'text/html') === false) {
            $this->context->buildViolation($constraint->message)->addViolation();
        } else {
            $addBookmarkDto->setResponse($response);
        }
    }
}