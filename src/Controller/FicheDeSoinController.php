<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FicheDeSoinController extends AbstractController
{
    /**
     * @Route("/fiche/de/soin", name="fiche_de_soin")
     */
    public function index(): Response
    {
        return $this->render('fiche_de_soin/index.html.twig', [
            'controller_name' => 'FicheDeSoinController',
        ]);
    }
}
