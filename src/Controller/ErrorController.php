<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'show')]
    public function show(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();

            if($session->get('token_jwt') != ''){
                return $this->render('bundles/TwigBundles/Exception/error404.html.twig', [
                    'controller_name' => 'CompanyController',
                    'login' => 'null',
                ]);
            }else{
                return $this->redirectToRoute('log_employer');
            }    
    }

}
