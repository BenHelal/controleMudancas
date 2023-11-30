<?php

namespace App\Controller\Software;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    #[Route('/software/developer', name: 'app_software_developer')]
    public function index(): Response
    {
        return $this->render('software/developer/index.html.twig', [
            'controller_name' => 'DeveloperController',
        ]);
    }
}
