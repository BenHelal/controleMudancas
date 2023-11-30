<?php

namespace App\Controller\Software;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestorController extends AbstractController
{
    #[Route('/software/gestor', name: 'app_software_gestor')]
    public function index(): Response
    {
        return $this->render('software/gestor/index.html.twig', [
            'controller_name' => 'GestorController',
        ]);
    }
}
