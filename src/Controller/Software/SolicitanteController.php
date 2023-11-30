<?php

namespace App\Controller\Software;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SolicitanteController extends AbstractController
{
    #[Route('/software/solicitante', name: 'app_software_solicitante')]
    public function index(): Response
    {
        return $this->render('software/solicitante/index.html.twig', [
            'controller_name' => 'SolicitanteController',
        ]);
    }
}
