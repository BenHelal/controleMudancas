<?php

namespace App\Controller\Software;

use App\Entity\Mudancas;
use App\Entity\Person;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class GestorController extends AbstractController
{


    /**
     * Initializes the GestorController.
     *
     * @Route("/software/gestor/init/{id}", name="app_software_gestor_init")
     * @return Response The response object.
     */
    public function init(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $muds = $mud->getMudS();
            dd($muds->getIniciar());
            return $this->render('software/gestor/documentation.html.twig', [
                'login' => 'null',
                'person' => $person,
                'controller_name' => 'GestorController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
    /**
     * Renders the documentation page for the GestorController.
     *
     * @Route("/software/gestor/documentation/{id}", name="app_software_gestor_documentation")
     * @return Response
     */
    public function documentation(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $muds = $mud->getMudS();
            return $this->render('software/gestor/documentation.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'controller_name' => 'GestorController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }

    }
    
    /**
     * Renders the steps page for the software gestor.
     *
     * @Route("/software/gestor/steps", name="app_software_gestor_steps")
     * @return Response
     */
    public function steps():Response
    {
        return $this->render('software/gestor/steps.html.twig', [
            'login' => 'null',
            'controller_name' => 'GestorController',
        ]);
    }


    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/gestor/test", name="app_software_gestor_test")
     * @return Response
     */
    public function test(): Response
    {
        return $this->render('software/gestor/test.html.twig', [
            'login' => 'null',
            'controller_name' => 'GestorController',
        ]);
    }    
    
}
