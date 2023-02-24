<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Client;
use App\Entity\ConfigEmail;
use App\Entity\Departemant;
use App\Entity\Email;
use App\Entity\Manager;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Process;
use App\Entity\Requestper;
use App\Entity\Sector;
use App\Entity\SectorProcess;
use App\EntityExt\TokenData;
use App\Form\GerenteMudType;
use App\Form\MudancasgestorImpType;
use App\Form\MudancasgestorType;
use App\Form\MudancasManagerType;
use App\Form\MudancasgestorToAppType;
use App\Form\MudancasType;
use App\Model\Class\IpAdress;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class MudancasController extends AbstractController
{
    #[Route('/mudancas', name: 'app_mudancas')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();
        $ipAdress = new IpAdress();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            //dd($session->get('name'));
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            if ($req ==  null) {
                return $this->redirectToRoute('app_request');
            }
            $dep = $em->getRepository(Departemant::class)->findOneBy(['name' => $person->getDepartemant()]);
            $manager = $em->getRepository(Sector::class)->findBy(['manager' => $person]);
            $mudanca = $em->getRepository(Mudancas::class)->findAll();
            $mudancas = [];
            $gestorArray = $em->getRepository(Mudancas::class)->findBy(['mangerMudancas' => $person]);
            $gestor = false;
            $notifGestor = [];
            foreach ($gestorArray as $key => $value) {
                if ($value->getDone() != 'Feito') {
                    array_push($notifGestor, $value);
                    $gestor = true;
                } elseif ($value->getDone() == 'Feito' && $value->getImplemented() == null) {
                    array_push($notifGestor, $value);
                    $gestor = true;
                }
            }

            //get the mudancas with the same Sector 
            if ($manager != null) {
                foreach ($mudanca as $key => $value) {
                    $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $value]);
                    $sp = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);

                    foreach ($sp as $key => $value2) {
                        if ($value->getAreaResp() == $person->getFunction() && $value->getDone() != 'Feito') {
                            array_push($mudancas, $value);
                        } else {
                            foreach ($value->getAreaImpact() as $key => $val) {
                                if ($val == $value->getAreaResp()) {
                                    if ($val == $person->getFunction() && $value->getDone() != 'Feito') {
                                        array_push($mudancas, $value);
                                    }
                                } elseif ($val != $value->getAreaResp()) {
                                    /* if ($val == $person->getFunction() && $value->getDone() != 'Feito' && $mudancas[sizeof($mudancas) - 1] != $value) {
                                        array_push($mudancas, $value);
                                    }*/
                                }
                            }
                        }
                    }
                }
            } else {

                foreach ($mudanca as $key => $value) {
                    if ($value->getAddBy() == $person && $value->getDone() != 'Feito') {
                        array_push($mudancas, $value);
                    } elseif ($value->getMangerMudancas() == $person && $value->getDone() != 'Feito') {
                        array_push($mudancas, $value);
                    }
                }
                //dd($mudancas);
            }


            $array = [];

            foreach ($mudanca as $key => $muda) {
                if ($muda->getAddBy() == $person && $muda->getImplemented() == null) {
                    array_push($array, $muda);
                } elseif ($muda->getAddBy() != $person && $muda->getMangerMudancas() == $person  && $muda->getImplemented() == null) {
                    array_push($array, $muda);
                } elseif ($muda->getAddBy() != $person && $muda->getMangerMudancas() != $person && $muda->getManagerUserAdd() == $person  && $muda->getImplemented() == null) {
                    array_push($array, $muda);
                } else {
                    $areaImpact =  $muda->getAreaImpact();
                    $mangerArea = false;
                    foreach ($areaImpact as $key => $value) {
                        if ($value->getManager() == $person) {
                            $mangerArea = true;
                        }
                    }
                    if ($mangerArea  && $muda->getImplemented() == null) {
                        array_push($array, $muda);
                    }
                }
            }

            $val = sizeof($mudanca);
            $val2 = 0;
            $arr = [];
            for ($i = 0; $i < sizeof($mudanca); $i++) {
                array_push($arr, $mudanca[$i]->getId());
                if ($mudanca[$i]->getDone() != 'Feito') {
                    $val2 = $val2 + 1;
                }
            }

            // dd($val2);
            $val = intval($this->presentNotDone($val, $val2));
            $size = sizeof($mudanca);

            if ($req->getApproves() == 'yes') {
                if ($manager == null && $gestor == false) {
                    return $this->render('mudancas/index.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => array_reverse($mudancas),
                        'manager' => false,
                        'person' => $person,
                        'gestor' => false
                    ]);
                } elseif ($gestor == true  && $manager == null) {
                    return $this->render('mudancas/index.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => array_reverse($mudancas),
                        'manager' => false,
                        'percent' => $val,
                        'size' => $size,
                        'gestor' => $gestor,
                        'ln' => $notifGestor,
                        'person' => $person
                    ]);
                } else {
                    return $this->render('mudancas/index.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => array_reverse($mudanca),
                        'manager' => true,
                        'percent' => $val,
                        'size' => $size,
                        'gestor' => false,
                        'ln' => $array,
                        'person' => $person
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_request');
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    public function presentNotDone($all, $notdone)
    {
        $val = $all - $notdone;
        if ($val == 0) {
            return 100;
        } else {
            $val2 = ($all - $notdone);
            $val2 = $val2 / $all;
            $val = $val2 * 100;
            return $val;
        }
    }

    #[Route('/approveImp/{id}', name: 'imp')]
    public function imp(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            $mud = $em->getRepository(Mudancas::class)->find($id); // condition on  situation of Mudanacas
            $data = $request->request->get('hiddenInput');
            //hiddenInput
            //dd($data);
            if ($mud == null) {
                return $this->redirectToRoute('app_mudancas');
            }
            $mud->setImplemented(1);
            $em->flush();
            foreach ($mud->getAreaImpact() as $key => $value) {
                $email = new  Email();
                $email->setMudancas($mud);
                $email->setSendTo($value->getManager());
                $email->setSendBy($person);
                $email->setTitle('Mudança implementada');
                $email->setBody('gestorAppToArea');
                $em->persist($email);
                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
            }
            $email = new  Email();
            $email->setMudancas($mud);
            $email->setSendTo($mud->getAddBy());
            $email->setSendBy($person);
            $email->setTitle('Mudança implementada');
            $email->setBody('gestorAppToUser');
            $em->persist($email);
            $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

            return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/rejectImp/{id}', name: 'impnoa')]
    public function impnoa(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            $mud = $em->getRepository(Mudancas::class)->find($id); // condition on  situation of Mudanacas
            if ($mud == null) {
                return $this->redirectToRoute('app_mudancas');
            }
            $data = $request->request->get('hiddenInput2');

            $mud->setImplemented(2);
            $em->flush();
            $email = new  Email();
            $email->setMudancas($mud);
            $email->setSendTo($mud->getAddBy());
            $email->setSendBy($person);
            $email->setTitle('Mudança não implementada');
            $email->setBody('gestorRejToUser');
            $em->persist($email);
            $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

            return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }


    #[Route('/approve/{id}', name: 'approve')]
    public function approve_by_id(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $manager = false;
            // condition on  situation of Mudanacas
            if ($mud == null) {
                return $this->redirectToRoute('app_mudancas');
            }

            if ($mud->getManagerUserApp() != null) {
                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
            }

            if ($req->getApproves() == 'yes') {
                if ($person->getPermission() != 'ler') {

                    if ($mud->getAddBy()->getFunction()->getManager()->getId() != $person->getId()) {
                        return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                    } else {
                        /*foreach ($mud->getAreaImpact() as $key => $value) {
                           if($mud->getManagerUserAdd() != $person){
                            $notManager = false;
                           }elseif($value->getManager() == $person){
                                $notManager = false;
                            }
                        }
                        if($notManager == false){
                            return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                        }
                        if ($mud->getManagerUserComment() != null) {
                            return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                        } else {*/
                        $area = [];
                        foreach ($mud->getAreaImpact() as $key => $value) {
                            if ($value->getManager() == $person) {
                                array_push($area, $value);
                            }
                        }
                        $form = $this->createForm(GerenteMudType::class, $mud);
                        $form->handleRequest($request);

                        if ($form->isSubmitted()) {

                            if ($mud->getManagerUserApp() == 2) {
                                $mud->setImplemented(2);
                                $mud->setDone('Feito');
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle('reject gerente controle do mudancas');
                                $email->setBody('reject_ger');
                                $em->persist($email);
                                $em->flush();
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                            } else {
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle('approved gerente controle do mudancas');
                                $email->setBody('approved_ger');
                                $em->persist($email);
                                $em->flush();
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAreaResp()->getManager());
                                $email->setSendBy($person);
                                $email->setTitle('ÁREA Resp gerente');
                                $email->setBody('managerAreaResp');
                                $em->persist($email);
                                $em->flush();
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                                foreach ($mud->getAreaImpact() as $key => $value) {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($value->getManager());
                                    $email->setSendBy($person);
                                    $email->setTitle('ÁREA IMPACTADA');
                                    $email->setBody('managerArea');
                                    $em->persist($email);
                                    $em->flush();
                                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                }
                            }

                            return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                        }
                        return $this->render('mudancas/manager.html.twig', [
                            'controller_name' => 'Atualizar Mudancas',
                            'login' => 'null',
                            'form' => $form->createView(),
                            'person' => $person,
                            'mud' => $mud,
                            'data' => $mud,
                            'area' => $area
                        ]);
                        //}
                    }
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

    #[Route('/createMudancas', name: 'cm')]
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            $manager = false;
            if ($req->getApproves() == 'yes') {
                if ($person->getPermission() != 'ler') {
                    $mud = new Mudancas();
                    $process = new Process();
                    date_default_timezone_set("America/Sao_Paulo");
                    $time = new \DateTime();
                    $time->format('Y-m-d H:i:s');
                    $mud->setDataCreation($time);
                    $mud->setAddBy($person);
                    $form = $this->createForm(MudancasType::class, $mud);
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $email = new  Email();
                        $email->setMudancas($mud);
                        $email->setSendTo($person);
                        $email->setSendBy($person);
                        $email->setTitle('Novo mudancas');
                        $email->setBody('create');
                        $em->persist($email);
                        $em->flush();

                        if ($mud->getNansenNumber() == null) {

                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($person->getFunction()->getManager());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação Gerente do solicitante');
                            $email->setBody('manager1');
                            $em->persist($email);
                            $em->flush();

                            if ($mud->getAddBy() == $person && $mud->getAddBy()->getFunction()->getManager() == $person) {
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle('approved gerente controle do mudancas');
                                $email->setBody('approved_ger');
                                $em->persist($email);
                                $em->flush();
                                //$this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAreaResp()->getManager());
                                $email->setSendBy($person);
                                $email->setTitle('ÁREA Resp gerente');
                                $email->setBody('managerAreaResp');
                                $em->persist($email);
                                $em->flush();
                                //$this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                                foreach ($mud->getAreaImpact() as $key => $value) {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($value->getManager());
                                    $email->setSendBy($person);
                                    $email->setTitle('ÁREA IMPACTADA');
                                    $email->setBody('managerArea');
                                    $em->persist($email);
                                    $em->flush();
                                    //    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                }
                            }
                        } else {
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($person->getFunction()->getManager());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação Gerente do solicitante');
                            $email->setBody('nansen');
                            $em->persist($email);
                            $em->flush();

                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($mud->getAddBy());
                            $email->setSendBy($person);
                            $email->setTitle('approved gerente controle do mudancas');
                            $email->setBody('approved_ger');
                            $em->persist($email);
                            $em->flush();
                            //$this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($mud->getAreaResp()->getManager());
                            $email->setSendBy($person);
                            $email->setTitle('ÁREA Resp gerente');
                            $email->setBody('managerAreaResp');
                            $em->persist($email);
                            $em->flush();
                            //$this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                            foreach ($mud->getAreaImpact() as $key => $value) {
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($value->getManager());
                                $email->setSendBy($person);
                                $email->setTitle('ÁREA IMPACTADA');
                                $email->setBody('managerArea');
                                $em->persist($email);
                                $em->flush();
                                //    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                            }
                        }
                        $sec = $em->getRepository(Sector::class)->findBy(['manager' => $person]);
                        foreach ($sec as $key => $value) {
                            if ($value === $mud->getAreaResp()) {
                                $mud->setApproved('approved');
                                $manager = true;
                            }
                        }
                        $em->persist($mud);
                        $em->flush();


                        /**
                         * Send Email
                         * -------------------------------------------------------
                         **/
                        $emails = $em->getRepository(Email::class)->findBy(['mudancas' => $mud]);
                        $ems = [];
                        foreach ($emails as $key => $value) {
                            if ($value->getClient() == null) {
                                $this->sendEmail($doctrine, $request, $value->getSendTo(), $value->getMudancas(), $value->getSendBy(), $value->getBody(), false);
                            } else {
                                $this->sendEmail($doctrine, $request, $value->getClient(), $value->getMudancas(), $value->getSendBy(), $value->getBody(), false, $value->getClient());
                            }
                        }
                        /**
                         * ------------------------------------------------------------
                         **/

                        $nansenName =  $form["nansenName"]->getData();
                        if ($nansenName != "") {
                            $mud->setApproved('approved');
                            $process->setMudancas($mud);
                            $process->setStatus('created');
                            $em->persist($process);
                            $em->flush();
                            $areaImpact = $mud->getAreaImpact();
                            $areaResp = $mud->getAreaResp();
                            $theyAreTheSame = false;

                            $mud->setManagerUserComment('Projeto Nansen');
                            $mud->setManagerUserApp(1);

                            foreach ($areaImpact as $key => $value) {
                                if ($value == $areaResp) {
                                    $theyAreTheSame = true;
                                }
                                $SectorProcess = new SectorProcess();
                                $SectorProcess->setProcess($process);
                                $SectorProcess->setSector($value);
                                $SectorProcess->setPerson($value->getManager());
                                $em->persist($SectorProcess);
                                $em->flush();
                            }
                            if ($theyAreTheSame == false) {
                                $SectorProcess = new SectorProcess();
                                $SectorProcess->setProcess($process);
                                $SectorProcess->setSector($areaResp);
                                $SectorProcess->setComment("validar pelo Código Nansen");
                                $em->persist($SectorProcess);
                                $em->flush();
                            }
                        } elseif ($mud->getAddBy() == $mud->getAddBy()->getFunction()->getManager()) {
                            $mud->setManagerUserAdd($person->getFunction()->getManager());
                            $process->setMudancas($mud);
                            $process->setStatus('created');
                            $em->persist($process);
                            $em->flush();
                            $areaImpact = $mud->getAreaImpact();
                            $areaResp = $mud->getAreaResp();
                            $mud->setManagerUserComment('como gerente de ' . $mud->getAddBy()->getFunction()->getName());
                            $mud->setManagerUserApp(1);
                            $theyAreTheSame = false;
                            foreach ($areaImpact as $key => $value) {
                                if ($value == $areaResp) {
                                    $theyAreTheSame = true;
                                }
                                $SectorProcess = new SectorProcess();
                                $SectorProcess->setProcess($process);
                                $SectorProcess->setSector($value);
                                $SectorProcess->setPerson($value->getManager());
                                $em->persist($SectorProcess);
                                $em->flush();
                            }

                            $em->flush();

                            if ($manager) {
                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                            }
                        } else {

                            $mud->setManagerUserAdd($person->getFunction()->getManager());
                            $process->setMudancas($mud);
                            $process->setStatus('created');
                            $em->persist($process);
                            $em->flush();
                            $areaImpact = $mud->getAreaImpact();
                            $areaResp = $mud->getAreaResp();

                            $theyAreTheSame = false;
                            foreach ($areaImpact as $key => $value) {
                                if ($value == $areaResp) {
                                    $theyAreTheSame = true;
                                }
                                $SectorProcess = new SectorProcess();
                                $SectorProcess->setProcess($process);
                                $SectorProcess->setSector($value);
                                $SectorProcess->setPerson($value->getManager());
                                $em->persist($SectorProcess);
                                $em->flush();
                            }

                            /*
                            if ($theyAreTheSame == false) {
                                $SectorProcess = new SectorProcess();
                                $SectorProcess->setProcess($process);
                                $SectorProcess->setSector($areaResp);
                                $SectorProcess->setPerson($areaResp->getManager());
                                $em->persist($SectorProcess);
                                $em->flush();
                            }*/
                            //if($mud->getApproved() == null ){$mud->setApproved('created');}
                            $em->flush();

                            if ($manager) {
                                return $this->redirectToRoute('approve', ['id' => $mud->getId()]);
                            }
                        }
                        return $this->redirectToRoute('app_mudancas');
                    }
                    return $this->render('mudancas/update.html.twig', [
                        'controller_name' => 'Atualizar Mudancas',
                        'login' => 'null',
                        'creation' => 'true',
                        'gestor' => false,
                        'form' => $form->createView(),
                        'person' => $person,
                        'manager' => false
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

    #[Route('/updateMudancas/{id}', name: 'upm')]
    public function update(ManagerRegistry $doctrine, Request $request, $id)
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
            // condition on  situation of Mudanacas
            if ($mud == null) {
                return $this->redirectToRoute('app_mudancas');
            } else {
                /**
                 * get the Process with mudancas 
                 * to can get The Sector 
                 * and check the situation of the mudancas  
                 */
                $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mud]);

                /**
                 * get the List of SectorProcess 
                 * to can access to the sector 
                 */
                $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);

                // get the Sector of the Person 
                $sec = $em->getRepository(Sector::class)->findBy(['manager' => $person]);

                /**
                 * if check and update the mudanca to close it :
                 */
                $NumberApproved = 0;
                $areaResp =  $mud->getAreaResp();
                if ($mud->getAppMan() == 1 &&  $mud->getAppGest() == 1) {
                    foreach ($mud->getAreaImpact() as $key => $value) {
                        # code...

                        $conn = $doctrine->getConnection();
                        $sql = 'SELECT sp.id FROM 
                                sector_process as sp,
                                mudancas as mud,
                                process as p
                                where 
                                    sp.sector_id = ? and
                                    p.id = ? and
                                    p.mudancas_id =  mud.id and 
                                    sp.process_id = p.id
                                            ';
                        $stmt = $conn->prepare($sql);
                        $stmt->bindValue(1, $value->getId());
                        $stmt->bindValue(2, $process->getId());
                        $resultSet = $stmt->executeQuery();
                        // get the id of the Porcess
                        // dd($process->getId());

                        $dm =  $resultSet->fetchAllAssociative();
                        if (isset($dm[0]) && ($dm !== null)) {
                            $SectorProcess = $em->getRepository(SectorProcess::class)->find($dm[0]["id"]);
                            if ($SectorProcess->getAppSectorMan() == 1) {
                                $NumberApproved = $NumberApproved + 1;
                            }
                        }
                        if ($NumberApproved == sizeof($mud->getAreaImpact())) {
                            //dd('test');
                            $mud->setDone('Feito');
                            date_default_timezone_set("America/Sao_Paulo");
                            $time = new \DateTime();
                            $time->format('Y-m-d H:i:s');
                            $mud->setDateOfImp($time);
                        }
                        $em->persist($mud);
                        $em->flush();
                    }
                } else {
                    if ($mud->getAppMan() == null || $mud->getAppGest() == null) {
                    } elseif ($mud->getAppMan() == 2 || $mud->getAppGest() == 2) {
                        $mud->setImplemented(2);
                        $mud->setDone('Feito');
                        $em->flush();
                    }
                }

                /**
                 * If the Mudancas is not null so :
                 * will cheack if the user is manager 
                 * or gestor 
                 * or manager of the user add the mudancas 
                 * or normal user 
                 */
                $manager = false;
                $gestor = false;


                // Request check
                if ($req->getApproves() == 'yes') {
                    // Permission check
                    if ($person->getPermission() != 'ler') {
                        if ($sec != null) {
                            // check the manager of the area Responsibla 
                            foreach ($sec as $key => $value) {
                                if ($value === $mud->getAreaResp()) {
                                    $manager = true;
                                }
                            }
                        } elseif ($sec == null) {
                            $manager = false;
                        }

                        // check the manager of the Mudancas 
                        if ($mud->getMangerMudancas() != null) {
                            if ($mud->getMangerMudancas()->getId() == $person->getId()) {
                                $gestor = true;
                            }
                        }
                        $itNull = true;
                        $numberOfArea = 0;
                        $mangerOfAreaDidntApp = false;
                        //fetch the sectorPress 
                        foreach ($sps as $key => $sp) {
                            /**
                             *  Check if there is one of the manager reject the mudancas
                             *  then close the Mudancas
                             */
                            if ($sp->getAppSectorMan() == 2 && $sp->getAppSectorMan() != null) {
                                $mud->setImplemented(2);
                                $mud->setDone('Feito');
                                $em->flush();
                            } elseif ($sp->getAppSectorMan() == null) {
                                $mangerOfAreaDidntApp = true;
                            }
                        }

                        $areaResp =  $mud->getAreaResp();
                        $gestMudancas = $mud->getMangerMudancas();


                        // check which Form need 
                        $form = null;


                        if ($manager == true && $gestor == false && $mangerOfAreaDidntApp == false) {
                            $form = $this->createForm(MudancasManagerType::class, $mud);
                        } elseif ($gestor == true && $mangerOfAreaDidntApp == false ) {
                            $form = $this->createForm(MudancasgestorToAppType::class, $mud);
                            $formImp = $this->createForm(MudancasgestorImpType::class, $mud);
                        } elseif ($manager == true && $gestor != true) {
                            $form = $this->createForm(MudancasManagerType::class, $mud);
                        } elseif ( $gestor == true && $mangerOfAreaDidntApp == true) {
                            $form = $this->createForm(MudancasgestorToAppType::class, $mud);
                        } else {
                            $form = $this->createForm(MudancasType::class, $mud);
                        }
                        // event listner 
                        $form->handleRequest($request);
                        //var of to check number of approved of area impacted
                        $NumberApproved = 0;


                        if ($mud->getAppMan() != null) {
                            if ($mud->getAppMan() == 2) {
                                $mud->setImplemented(2);
                                $mud->setDone('Feito');
                                $em->flush();
                            }
                        }

                        if ($gestor == true && $mangerOfAreaDidntApp == false) {
                            //dd('etst');
                            $formImp->handleRequest($request);
                            if ($formImp->isSubmitted()) {
                                $desImp = $mud->getImpDesc();
                                $file2 = $mud->getPdf();
                                $filePDF = $mud->getPdf();
                                if ($file2 != null) {

                                    $pdfname = $file2->getClientOriginalName();
                                    $mud->setNamePdf($pdfname);
                                    $ex = strval($file2->guessExtension());
                                    date_default_timezone_set("America/Sao_Paulo");
                                    $time = new \DateTime();
                                    //$fileName2 = md5(uniqid()).'.'.$file2->guessExtension();
                                    $fileName2 = $mud->getId() . '.' . $ex;
                                    $publicDirectory = $this->getParameter('kernel.project_dir');
                                    $excelFilepath2 =  $publicDirectory . '/public/assets/' . $mud->getId();
                                    $file2->move($excelFilepath2, $fileName2);
                                    $mud->setPdf($fileName2);
                                }
                                $file = $mud->getPhoto();
                                if ($file != null) {
                                    date_default_timezone_set("America/Sao_Paulo");
                                    $time = new \DateTime();
                                    $fileName = $mud->getId() . '.' . $file->guessExtension();
                                    $publicDirectory = $this->getParameter('kernel.project_dir');
                                    $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
                                    $file->move($excelFilepath, $fileName);
                                    $mud->setPhoto($fileName);
                                }

                                $em->flush();
                                if ($mud->getImplemented() == 1) {
                                    return $this->redirectToRoute('imp', ['id' => $mud->getId()]);
                                } elseif ($mud->getImplemented() == 2) {
                                    return $this->redirectToRoute('impnoa', ['id' => $mud->getId()]);
                                }
                            }
                        }


                        if ($form->isSubmitted() && $form->isValid()) {
                            /**
                             * check if the person connect is manager 
                             * or gestor 
                             * or normal user
                             */
                            $anotherEm = $doctrine->getManager('database2');
                            if ($manager && $gestor == false) {
                                /**
                                 * check manager approve 
                                 */
                                $mud->setAppMan($form["appMan"]->getData());
                                $mud->setComMan($form["comMan"]->getData());
                                $em->persist($mud);
                                $em->flush();


                                /**
                                 * Send Email
                                 * -------------------------------------------------------
                                 **/
                                if($mud->getClient() != null){
                                    foreach ($mud->getClient() as $key => $value) {
                                        # code...
                                        $token = new ApiToken($value, $mud);
                                        $em->persist($token);
        
                                        $tok = new TokenData();
                                        $anotherEm->persist($tok);
                                        //$customers = $anotherEm->getRepository(TokenData::class, 'database2')->findAll();
                                        
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setClient($value);
                                        $email->setSendBy($person);
                                        $email->setTitle('Aprovação Client');
                                        $email->setBody('client');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $value, $mud, $person, 'client', false, $value);
                                    }
                                }
                                 
                                $emails = $em->getRepository(Email::class)->findBy(['mudancas' => $mud]);
                                $ems = [];
                                foreach ($emails as $key => $value) {
                                    if ($ems == null) {
                                        array_push($ems, $value);
                                    } else {
                                        $existe = false;
                                        foreach ($ems as $key => $value2) {
                                            if ($value->getSendTo() == $value2->getSendTo()) {
                                                $existe = true;
                                            }
                                        }
                                        if ($existe == false) {
                                            array_push($ems, $value);
                                        }
                                    }
                                    $em->remove($value);
                                    $em->flush();
                                }
                                foreach ($ems as $key => $value) {
                                    //$email = new Email();
                                    $email = $value;
                                    $em->persist($email);
                                    if ($value->getSendTo() != $value->getSendBy()) {
                                        $this->sendEmail($doctrine, $request, $value->getSendTo(), $value->getMudancas(), $value->getSendBy(), $value->getBody(), false);
                                    }
                                }
                                /**
                                 * ------------------------------------------------------------
                                 **/


                                /**
                                 * check if the manager is manager of other departemant 
                                 */
                                $manager_dep = [];
                                /**
                                 *this table to fetch the data from SectorProcess and put the item  
                                 */
                                $areaImpact =  $mud->getAreaImpact();

                                /**
                                 * check if the manager is the manager of the mudanacas
                                 * or manager of one of the Area Impact 
                                 * or manager of person add the mudancas 
                                 */
                                foreach ($areaImpact as $key => $ai) {
                                    if ($ai->getManager() == $person) {
                                        array_push($manager_dep, $ai);
                                    }
                                }

                                if ($mud->getAppMan() == 1) {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getAddBy());
                                    $email->setSendBy($person);
                                    $email->setTitle('Aprovado pelo Gerente do solicitante');
                                    $email->setBody('manager1APP');
                                    $em->persist($email);
                                    $em->flush();
                                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                                    foreach ($mud->getAreaImpact() as $key => $value) {
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($value->getManager());
                                        $email->setSendBy($person);
                                        $email->setTitle('ÁREA IMPACTADA');
                                        $email->setBody('managerArea');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    }

                                    /**
                                     * fetech the sector process
                                     */
                                    foreach ($sps as $key => $sp) {
                                        foreach ($manager_dep as $key => $md) {
                                            if ($sp->getSector() == $md) {
                                                $sp->setComment('Aprovado sem ressalva');
                                                $sp->setAppSectorMan($mud->getAppMan());
                                                $em->persist($sp);
                                                $em->flush();
                                            }
                                        }
                                    }

                                    if ($mud->getMangerMudancas() != null) {
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($mud->getMangerMudancas());
                                        $email->setSendBy($person);
                                        $email->setTitle('Notificação para o gerente da mudança');
                                        $email->setBody('gerente');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    }
                                } elseif ($mud->getAppMan() == 2) {
                                    $mud->setImplemented(2);
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getMangerMudancas());
                                    $email->setSendBy($person);
                                    $email->setTitle('Reprovado pelo Gerente do solicitante');
                                    $email->setBody('manager1Rej');
                                    $em->persist($email);
                                    $em->flush();
                                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                                    /**
                                     * check if rejected 
                                     * so will reject for all the other area managed by the same person 
                                     */
                                    foreach ($sps as $key => $sp) {
                                        foreach ($manager_dep as $key => $md) {
                                            if ($sp->getSector() == $md) {
                                                $sp->setComment('Reprovado sem ressalva');
                                                $sp->setAppSectorMan($mud->getAppMan());
                                                $em->persist($sp);
                                                $em->flush();
                                            }
                                        }
                                    }
                                }
                                $em->persist($mud);
                                $em->flush();
                                $anotherEm->flush();
                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                            } elseif ($gestor) {
                                
                                /**
                                * get the Process with mudancas 
                                * to can get The Sector 
                                * and check the situation of the mudancas  
                                */
                                $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mud]);

                                /**
                                * get the List of SectorProcess 
                                * to can access to the sector 
                                */
                                $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
                                $number_sector_app = 0;
                                
                                //dd($sps);
                                foreach ($sps as $key => $value) {
                                    if($value->getAppSectorMan() != null){
                                        $number_sector_app++;
                                    };
                                }
                                
                                if(sizeof($sps) == $number_sector_app){
                                    $mud->setDone('Feito');
                                }

                                //dd($mud->getDone());
                                if ($mud->getAppGest() == 1) {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getAddBy());
                                    $email->setSendBy($person);
                                    $email->setTitle('Aprovado pelo gerente da mudança');
                                    $email->setBody('approved_ger');
                                    $em->persist($email);
                                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getAddBy());
                                    $email->setSendBy($person);
                                    $email->setTitle('Aprovado pelo gestor é encaminhado um e-mail para o solicitante notificando-o');
                                    $email->setBody('gestorAppToUser');
                                    $em->persist($email);
                                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                } elseif ($mud->getAppGest() == 2) {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getAddBy());
                                    $email->setSendBy($person);
                                    $email->setTitle('Reprovado pelo gestor é enviado um e-mail notificando o solicitante e gerente do gestor da mudança.');
                                    $email->setBody('gestorRejToUser');
                                    $em->persist($email);
                                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                }
                                $mud->setAreaResp($areaResp);
                                //$mud->setPdf($filePDF);
                                $mud->setMangerMudancas($gestMudancas);
                                $em->flush();

                                $mangerOfAreaDidntApp = false;
                                //fetch the sectorPress 
                                foreach ($sps as $key => $sp) {
                                    /**
                                     *  Check if there is one of the manager reject the mudancas
                                     *  then close the Mudancas
                                     */
                                    if ($sp->getAppSectorMan() == 2 && $sp->getAppSectorMan() != null) {
                                        $mud->setImplemented(2);
                                        $mud->setDone('Feito');
                                        $em->flush();
                                    } elseif ($sp->getAppSectorMan() == null) {
                                    }
                                }

                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                            } else {
                            }
                        }
                        if ($gestor == true && $mangerOfAreaDidntApp == false) {
                            return $this->render('mudancas/update.html.twig', [
                                'controller_name' => 'Atualizar Mudancas',
                                'login' => 'null',
                                'creation' => 'false',
                                'person' => $person,
                                'm' => $mud,
                                'mangerOfAreaDidntApp' => $mangerOfAreaDidntApp,
                                'manager' => $manager,
                                'gestor' => $gestor,
                                'formImp' => $formImp->createView(),
                                'form' => $form->createView(),
                            ]);
                        } else {
                            return $this->render('mudancas/update.html.twig', [
                                'controller_name' => 'Atualizar Mudancas',
                                'login' => 'null',
                                'creation' => 'false',
                                'person' => $person,
                                'm' => $mud,
                                'mangerOfAreaDidntApp' => $mangerOfAreaDidntApp,
                                'manager' => $manager,
                                'gestor' => $gestor,
                                'form' => $form->createView(),
                            ]);
                        }
                    } else {
                        return $this->redirectToRoute('app_mudancas');
                    }
                } else {
                    return $this->redirectToRoute('app_request');
                }
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/file/{id}', name: 'filed')]
    public function filexdqzxdqzxd(ManagerRegistry $doctrine, Request $request, $id)
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
            $filePath = 'public/assets/' . $id . '/' . $mud->getPdf();

            $ipAdress = new IpAdress();

            $url = 'http://' . $ipAdress->getIpAdress() . '/controleMudancas/' . $filePath;
            $response = new RedirectResponse($url);
            $response->send();
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/History/Mudancas', name: 'all_mud')]
    public function all_mudancas(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            //dd($session->get('name'));
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            if ($req ==  null) {
                return $this->redirectToRoute('app_request');
            }
            $dep = $em->getRepository(Departemant::class)->findOneBy(['name' => $person->getDepartemant()]);
            $manager = $em->getRepository(Manager::class)->findOneBy(['person' => $person]);
            $mudancas = $em->getRepository(Mudancas::class)->findAll();
            $listNotif = [];
            $conn = $doctrine->getConnection();
            $sql = '
                select 
                    pr.id as idPr,
                    mud.id as idMud 
                FROM 
                    mudancas as mud, 
                    process as pr, 
                    sector_process as dp,
                    sector as d,
                    person as p,
                    departemant as dep
                WHERE 
                    mud.id = pr.mudancas_id AND 
                    dp.process_id = pr.id AND 
                    dp.sector_id = d.id AND
                    d.departemant_id = dep.id AND
                    dep.name = p.departemant AND
                    p.id = :person AND 
                    pr.status = "created"';
            $stmt = $conn->prepare($sql);
            //dd($person);
            $resultSet = $stmt->executeQuery(['person' => $person->getId()]);
            $ln =  $resultSet->fetchAllAssociative();

            foreach ($ln as $notif) {
                $id = $notif['idMud'];
                $mud =  $em->getRepository(Mudancas::class)->find($id);
                array_push($listNotif, $mud);
            }

            $mudancas = $em->getRepository(Mudancas::class)->findAll();
            $val = sizeof($mudancas);
            $val2 = 0;
            $arr = [];
            for ($i = 0; $i < sizeof($mudancas); $i++) {
                array_push($arr, $mudancas[$i]->getId());
                if ($mudancas[$i]->getDone() != 'Feito') {
                    $val2 = $val2 + 1;
                }
            }
            // dd($val2);
            $val = intval($this->presentNotDone($val, $val2));
            $size = sizeof($mudancas);

            if ($req->getApproves() == 'yes') {
                /*              $dep =  $em->getRepository(Departemant::class)->findOneBy(['name' => $person->getDepartemant()]);
                // if($person->getRole()==)
                $depMud = $em->getRepository(DepartemantMudancass::class)->findBy(['Departemant' => $dep]);
                $mudancas = [];
                foreach ($depMud as $dm) {
                    array_push($mudancas, $dm->getMudancas());
                }
                */
                if ($manager == null) {
                    return $this->render('mudancas/history.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => array_reverse($mudancas),
                        'manager' => false,
                        'person' => $person,
                        'gestor' => false
                    ]);
                } else {
                    return $this->render('mudancas/history.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => array_reverse($mudancas),
                        'manager' => true, 'percent' => $val,
                        'size' => $size,
                        'gestor' => false,
                        'ln' => $listNotif,
                        'person' => $person
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_request');
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    public function sendEmail(ManagerRegistry $doctrine, Request $request, $sendTo, $mud, $per, $demand,  $gestor, $client = null)
    {

        $em = $doctrine->getManager();
        $config = $em->getRepository(ConfigEmail::class)->find(1);
        if ($config == null) {
            $config = new ConfigEmail();
            $config->setHost('smtp.office365.com');
            $config->setSmtpAuth(true);
            $config->setPort(587);
            $config->setUsername('noreply@serdia.com.br');
            $config->setPassword('9BhAsZw8a8ZrnQzX');
            $config->setEmailSystem('noreply@serdia.com.br');
            $config->setTitleObj('Serdia Control Mudanças');
            $config->setSubject('Controle de Mudanças');
            $config->setChartSet('UTF-8');
            $em->persist($config);
            $em->flush();
        }

        $mail = new PHPMailer(true);
        // check the manager of the Mudancas 
        try {

            $ipAdress = new IpAdress();

            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;   
            $mail->IsSMTP(); // Define que a mensagem será SMTP
            $mail->Host = $config->getHost(); // Endereço do servidor SMTP
            $mail->SMTPAuth = $config->isSmtpAuth(); // Usa autenticação SMTP? (opcional)
            $mail->Port = $config->getPort();
            $mail->Username = $config->getUsername(); // Usuário do servidor SMTP
            $mail->Password = $config->getPassword(); // Senha do servidor SMTP                           
            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $mail->setFrom($config->getEmailSystem(), $config->getTitleObj());
            if ($client != null) {
                $mail->AddAddress($client->getRespEmail(), $client->getResp());
                $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
                $mail->CharSet = $config->getChartSet(); // Charset da mensagem (opcional)
                $mail->Subject  = $config->getSubject();
                $mail->msgHTML($this->renderView('emails/myemail.html.twig', [
                    'name'      =>  'Controle de Mudanças',
                    'mud'       =>  $mud,
                    'sendTo'    => $config->getEmailSystem(),
                    'per'       =>  $per,
                    'c' => $client,
                    'ip'        => $ipAdress->getIpAdress(),
                    'name'      =>$client->getResp(),
                    'gestor'    => $gestor,
                    'demand'    =>  $demand
                ]));
            } else {
                $mail->AddAddress($sendTo->getEmail(), $sendTo->getName());
                $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
                $mail->CharSet = $config->getChartSet(); // Charset da mensagem (opcional)
                $mail->Subject  = $config->getSubject();
                $mail->msgHTML($this->renderView('emails/myemail.html.twig', [
                    'name'      =>  'Controle de Mudanças',
                    'mud'       =>  $mud,
                    'sendTo'    => $sendTo,
                    'per'       =>  $per,
                    'ip'        => $ipAdress->getIpAdress(),
                    'name'      => $sendTo->getName(),
                    'gestor'    => $gestor,
                    'demand'    =>  $demand
                ]));
            }


            //$mail->Subject  = "ASSUNTO"; // Assunto da mensagem
            //$mail->Body = "HTML FORMAT";

            // Envia o e-mail
            $mail->Send();
            return $mail;
            // Limpa os destinatários e os anexos
            // $mail->ClearAllRecipients();
            //$mail->ClearAttachments();

            return $this->redirectToRoute('app_mudancas');
        } catch (Exception $e) {
            //   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        return;
    }
}
