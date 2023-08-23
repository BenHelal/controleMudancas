<?php

namespace App\Controller;

use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Requestper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MudancasSoftwareController extends AbstractController
{
    #[Route('/mudancas/software/Gerente/Aprocao/{id}', name: 'gerente_software')]
    public function Gerente(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            // get User Connected data 
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            // get Request Of this user To acces 
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            // get Mudancas with ID
            $mud = $em->getRepository(Mudancas::class)->find($id);


            return $this->render('mudancas_software/gerente_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }


    #[Route('/mudancas/software/Gestor/Mudança/{id}', name: 'gestor_software')]
    public function Gestor(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            // get User Connected data 
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            // get Request Of this user To acces 
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            // get Mudancas with ID
            $mud = $em->getRepository(Mudancas::class)->find($id);


            return $this->render('mudancas_software/gestor_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas/software/Solicitante/Mudança/{id}', name: 'Solicitante_software')]
    public function Solicitante(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            // get User Connected data 
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            // get Request Of this user To acces 
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            // get Mudancas with ID
            $mud = $em->getRepository(Mudancas::class)->find($id);


            return $this->render('mudancas_software/Solicitante_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    
    #[Route('/mudancas/software/Dev/{id}', name: 'dev_software')]
    public function dev(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            // get User Connected data 
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            // get Request Of this user To acces 
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            // get Mudancas with ID
            $mud = $em->getRepository(Mudancas::class)->find($id);


            return $this->render('mudancas_software/dev_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas/software/test/ti/{id}', name: 'test_ti_software')]
    public function test_ti(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            // get User Connected data 
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            // get Request Of this user To acces 
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            // get Mudancas with ID
            $mud = $em->getRepository(Mudancas::class)->find($id);


            return $this->render('mudancas_software/test_ti_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas/software/test/solicitante/{id}', name: 'test_solicitante_software')]
    public function test_solicitante(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            // get User Connected data 
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            // get Request Of this user To acces 
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            // get Mudancas with ID
            $mud = $em->getRepository(Mudancas::class)->find($id);


            return $this->render('mudancas_software/test_solicitante_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }
}
