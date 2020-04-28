<?php


namespace Lenvendo\ArgumentResolver;


use Lenvendo\UserInteraction\Dto\Dto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DataParamsResolver implements ArgumentValueResolverInterface
{

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return in_array(Dto::class, class_implements($argument->getType()));
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        if ($request->isMethod('post') || $request->isMethod('put')) {
            yield $argument->getType()::createFromRequest($request);
        } else {
            yield null;
        }
    }
}