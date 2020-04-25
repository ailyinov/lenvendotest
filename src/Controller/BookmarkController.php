<?php


namespace Lenvendo\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookmarkController extends AbstractController
{
    public function addAction()
    {
        return $this->render('base.html.twig', [
            'sup' => 'sup',
        ]);
    }
}