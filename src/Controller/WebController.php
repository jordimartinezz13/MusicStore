<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebController extends AbstractController
{
    #[Route('/web', name: 'web')]
    public function index(): Response
    {
        return $this->render('web/index.html.twig', [
            'controller_name' => 'WebController',
        ]);
    }

    /**
    * @Route("/inicio", name="inicio")
    */
    public function inicio(): Response
    {
        return $this->redirectToRoute('web');
    }
}
