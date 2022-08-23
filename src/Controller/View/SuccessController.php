<?php

namespace App\Controller\View;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class SuccessController
 * @package App\Controller\View
 */
class SuccessController extends AbstractController
{
    /**
     * @return Response
     * 
     * @Route("/succes", name="success", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('front/success/success.html.twig');
    }
}
