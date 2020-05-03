<?php


namespace Lenvendo\UserInteraction\Validator\Constraint;


use Lenvendo\Entity\Bookmark;
use Lenvendo\Repository\BookmarkRepository;
use Lenvendo\UserInteraction\Dto\RemoveBookmarkDto;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BookmarkPasswordValidator extends ConstraintValidator
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var BookmarkRepository
     */
    private $bookmarkRepository;

    /**
     * BookmarkPasswordValidator constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param BookmarkRepository $bookmarkRepository
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, BookmarkRepository $bookmarkRepository)
    {
        $this->encoderFactory = $encoderFactory;
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * @param RemoveBookmarkDto $removeBookmarkDto
     * @param Constraint $constraint
     */
    public function validate($removeBookmarkDto, Constraint $constraint)
    {
        $password = $removeBookmarkDto->getPassword();
        if (null === $password || '' === $password) {
            $this->context->addViolation($constraint->message);

            return;
        }

        /** @var Bookmark $bookmark */
        $bookmark = $this->bookmarkRepository->find($removeBookmarkDto->getId());
        if (null === $bookmark) {
            $this->context->addViolation("Bookmark does not exists.");

            return;
        }

        $encoder = $this->encoderFactory->getEncoder($bookmark);

        if (null === $bookmark->getPassword() || !$encoder->isPasswordValid($bookmark->getPassword(), $password, $bookmark->getSalt())) {
            $this->context->addViolation($constraint->message);
        }
    }
}