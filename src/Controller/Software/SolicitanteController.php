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

class SolicitanteController extends AbstractController
{
    #[Route('/software/solicitante', name: 'app_software_solicitante')]
    public function index(): Response
    {
        return $this->render('software/solicitante/index.html.twig', [
            'controller_name' => 'SolicitanteController',
        ]);
    }



    /**
     * Renders the documentation page for the solicitanteController.
     *
     * @Route("/software/solicitante/approve/documentation/{id}", name="app_software_sol_documentation")
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

            //steps Gestor 
            $SD =  $muds->getStepsGestor();
            return $this->render('software/solicitante/documentation.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $SD,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
