<?php

namespace App\Controller;

use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Process;
use App\Entity\Requestper;
use App\Entity\SectorProcess;
use App\Form\SectorProcessType;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class NotifController extends AbstractController
{
    #[Route('/notif/{id}', name: 'app_notif')]
    public function index($id, ManagerRegistry $doctrine, Request $request): Response
    {

        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            $mudancas = $em->getRepository(Mudancas::class)->find($id);
            $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mudancas]);
            $oneOfSp = null;
            $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
            
            $notManager = true ;
            foreach ($mudancas->getAreaImpact() as $key => $value) {
                if($value->getCoordinator() == $person){
                    $notManager = false;
                }
            }

            if($notManager == true){
                return $this->redirectToRoute('upm', ['id' => $mudancas->getId()]);
            }
            $area = [];
            foreach ($mudancas->getAreaImpact() as $key => $value) {
                if ($value->getCoordinator() == $person) {
                    array_push($area, $value);
                }
            }


            foreach ($sps as $key => $value) {
                if ($value->getComment() == null) {
                    $oneOfSp = $value;
                }
            }

            if ($oneOfSp  != null) {
                $form = $this->createForm(SectorProcessType::class, $oneOfSp);
            } else {
                $form = $this->createForm(SectorProcessType::class, $sps[0]);
            }
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($sps as $key => $sp) {
                    if ($sp->getSector()->getManager() == $person) {
                        //if( $mudancas->getManager)
                        if ($oneOfSp != null) {
                            
                            $sp->setComment($oneOfSp->getComment());
                            $sp->setAppSectorMan($oneOfSp->getAppSectorMan());
                        }

                        $em->persist($sp);
                        $em->flush();
                        
                    }
                }

                return $this->redirectToRoute('upm', ['id' => $id]);
               /* if(){
                    $email = new  Email();
                    $email->setMudancas($mud);
                    $email->setSendTo($mud->getAddBy());
                    $email->setSendBy($person);
                    $email->setTitle('Aprovado pelo Gerente do solicitante');
                    $email->setBody('gestorAppToArea');
                    $em->persist($email);
                    $em->flush();
                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                    
                }elseif(){
                    $email = new  Email();
                                            $email->setMudancas($mud);
                                            $email->setSendTo($mud->getAddBy());
                                            $email->setSendBy($person);
                                            $email->setTitle('Aprovado pelo Gerente do solicitante');
                                            $email->setBody('manager1APP');
                                            $em->persist($email);
                                            $em->flush();
                                            $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                            
                }*/

            }


            if ($req->getApproves() == 'yes') {
                if ($person->getPermission() != 'ler') {
                    return $this->render('notif/index.html.twig', [
                        'controller_name' => 'NotifController',
                        'login' => 'null',
                        'creation' => 'true',
                        'person' => $person,
                        'area' => $area,
                        'form' => $form->createView(),
                        'data' => $mudancas
                    ]);
                } else {
                    return $this->redirectToRoute('app_mudancas');
                }
            } else {
                return $this->redirectToRoute('app_request');
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }
}
