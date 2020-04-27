<?php


namespace Lenvendo\UserInteraction\Validator\Constraint;


use Lenvendo\Repository\BookmarkRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BookmarkUniqueUrlValidator extends ConstraintValidator
{

    /**
     * @var BookmarkRepository
     */
    private $bookmarkRepository;

    /**
     * BookmarkUniqueUrlValidator constructor.
     *
     * @param BookmarkRepository $bookmarkRepository
     */
    public function __construct(BookmarkRepository $bookmarkRepository)
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        $bookmark = $this->bookmarkRepository->findOneByUrl($value);
        if (null !== $bookmark) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}