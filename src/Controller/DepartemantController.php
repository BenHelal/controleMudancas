<?php

namespace App\Controller;

use App\Entity\Departemant;
use App\Entity\Person;
use App\Entity\Requestper;
use App\Form\DepartemantType;
use App\Repository\DepartemantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DepartemantController extends AbstractController
{

    #[Route('/dep', name: 'app_departemant')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_jwt') != '') {

            $em = $doctrine->getManager();
            $name = $session->get('name');
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $name]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);

            if ($req->getApproves() == 'yes') {
                $dep = $doctrine->getRepository(Departemant::class)->findAll();

                return $this->render('departemant/index.html.twig', [
                    'controller_name' => 'Departamento',
                    'login' => 'null',
                    'creation' => 'null',
                    'person' => $person,
                    'dep' => $dep
                ]);
            } else {
                return $this->redirectToRoute('app_request');
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }


    /**
     * @Route("/new_dep",name="newDep")
     */
    /*public function create_departemant(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $dep = new Departemant();
            $em = $doctrine->getManager();
            $name = $session->get('name');
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $name]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);

            if ($req->getApproves() == 'yes') {
                $form = $this->createForm(DepartemantType::class, $dep);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $em = $doctrine->getManager();
                    $em->persist($dep);
                    $em->flush();
                    return $this->redirectToRoute('app_departemant');
                }
                return $this->render('departemant/index.html.twig', [
                    'controller_name' => 'Adicionar nova departamento',
                    'login' => 'null',
                    'creation' => 'true',
                    'form' => $form->createView(),
                ]);
            } else {
                return $this->redirectToRoute('app_request');
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }*/


    /**
     * @Route("/path/{id}",name="updateDep")
     */
    /*public function update_departemant(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {

            $em = $doctrine->getManager();
            $name = $session->get('name');
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $name]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);

            if ($req->getApproves() == 'yes') {
                $dep = $em->getRepository(Departemant::class)->find($id);
                $form = $this->createForm(DepartemantType::class, $dep);
                $form->handleRequest($request);


                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($dep);
                    $em->flush();
                    return $this->redirectToRoute('app_departemant');
                }
                return $this->render('departemant/index.html.twig', [
                    'controller_name' => 'Atualizar departamento',
                    'login' => 'null',
                    'creation' => 'false',
                    'form' => $form->createView(),
                ]);
            } else {
                return $this->redirectToRoute('app_request');
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }*/
}
