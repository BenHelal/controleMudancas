<?php

namespace App\Controller\Software;

use App\Model\Class\IpAdress;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MainSoftwareController extends AbstractController
{
    #[Route('/software/mainSoftware', name: 'app_software_main')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();
        $ipAdress = new IpAdress();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            //dd($session->get('name'));
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
        return $this->render('software/mainSoftware/index.html.twig', [
            'controller_name' => 'MainController',
            'login' => 'null',
        ]);}else{
            
            return $this->redirectToRoute('log_employer');
        }

    }
}
