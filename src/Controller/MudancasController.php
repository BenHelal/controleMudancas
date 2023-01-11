<?php

namespace App\Controller;

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
use App\Form\GerenteMudType;
use App\Form\MudancasgestorType;
use App\Form\MudancasManagerType;
use App\Form\MudancasType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
                }
            }

            //get the mudancas with the same Sector 
            if ($manager != null) {
                foreach ($mudanca as $key => $value) {
                    $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $value]);
                    $sp = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);

                    foreach ($sp as $key => $value2) {
                        /*
                        if ($value2->getAppSectorMan() != null && $value2->getAppSectorMan() != ) {
                            $value->setDone(' ');
                            $em->flush();
                        } elseif ($value2->isAppSectorMan() != null && $value2->isAppSectorMan() == 1) {
                            $value->setDone('Feito');
                            $em->flush();
                        } elseif ($value->getAppMan() == 2) {
                            $value->setDone('Feito');
                            $em->flush();
                        }*/

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
                /***
                 * Get Mudancas with list for the user connected for main page with filter
                 *  and put the mudanca in Mudancas[] after filter 
                 * Filter : -   open Mudancas 
                 *          -   mudancas with same Departemant 
                 *          -   mudancas related with user if he is the gestor
                 */

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
            foreach ($manager as $key => $ma) {
                foreach ($mudanca as $key => $m) {
                    foreach ($m->getAreaImpact() as $key => $value) {
                        //dd($array);
                        if ($array != null) {
                            $test = 0;
                            for ($i = 0; $i < sizeof($array); $i++) {
                                if ($m == $array[$i]) {
                                    $test++;
                                }
                            }
                            if ($test == 0) {
                                array_push($array, $m);
                            }
                        } elseif ($array == null && $m->getDone() != 'Feito') {
                            if ($value->getManager() == $person) {
                                array_push($array, $m);
                            }
                        }
                    }
                }
            }


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
                        'mud' => $mudancas,
                        'manager' => false,
                        'person' => $person,
                        'gestor' => false
                    ]);
                } elseif ($gestor == true  && $manager == null) {
                    return $this->render('mudancas/index.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => $mudancas,
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
                        'mud' => $mudanca,
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

            if ($req->getApproves() == 'yes') {
                if ($person->getPermission() != 'ler') {
                    if ($mud->getManagerUserAdd() != $person) {
                        return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                    } else {
                        if ($mud->getManagerUserComment() != null) {
                            return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                        } else {

                            $form = $this->createForm(GerenteMudType::class, $mud);
                            $form->handleRequest($request);

                            if ($form->isSubmitted() && $form->isValid()) {
                                if ($mud->getManagerUserApp() == 2) {
                                    $mud->setDone('Feito');
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getAddBy());
                                    $email->setSendBy($person);
                                    $email->setTitle('reject gerente controle do mudancas');
                                    $email->setBody('reject_ger');
                                    $em->persist($email);
                                    $em->flush();
                                } else {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getAddBy());
                                    $email->setSendBy($person);
                                    $email->setTitle('approved gerente controle do mudancas');
                                    $email->setBody('approved_ger');
                                    $em->persist($email);
                                    $em->flush();
                                }
                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                            }
                            return $this->render('mudancas/manager.html.twig', [
                                'controller_name' => 'Atualizar Mudancas',
                                'login' => 'null',
                                'form' => $form->createView(),
                                'person' => $person,
                                'mud' => $mud
                            ]);
                        }
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
                        foreach ($mud->getAreaImpact() as $key => $value) {
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($value->getManager());
                            $email->setSendBy($person);
                            $email->setTitle('Create new controle do mudancas');
                            $email->setBody('create');
                            $em->persist($email);
                            $em->flush();
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
                            $email = new Email();
                            $email = $value;
                            $em->persist($email);
                            $this->sendEmail($doctrine, $request, $value->getSendTo()->getEmail(), $value->getSendTo()->getName(), $value->getMudancas(), $value->getSendBy(), $value->getBody(), false);
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
                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
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
                    $conn = $doctrine->getConnection();
                    $sql = 'SELECT sp.id FROM 
                                            sector_process as sp,
                                            mudancas as mud,
                                            process as p
                                            where 
                                            sp.sector_id = ? and
                                            mud.id = ? and
                                            p.mudancas_id =  mud.id and 
                                            sp.process_id = p.id
                                            ';
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(1, $areaResp->getId());
                    $stmt->bindValue(2, $mud->getId());
                    $resultSet = $stmt->executeQuery();

                    // get the id of the Porcess
                    $dm =  $resultSet->fetchAllAssociative();
                    try {
                        if (isset($dm) && ($dm !== null)) {
                            $SectorProcess = $em->getRepository(SectorProcess::class)->find($dm[0]["id"]);
                            if ($SectorProcess->getAppSectorMan() == 1) {
                                $NumberApproved = $NumberApproved + 1;
                            }
                        }
                    } catch (Exception $e) {
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
                } else {

                    if ($mud->getAppMan() == null || $mud->getAppGest() == null) {
                    } elseif ($mud->getAppMan() == 2 || $mud->getAppGest() == 2) {
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

                        //fetch the sectorPress 
                        foreach ($sps as $key => $sp) {
                            /**
                             *  Check if there is one of the manager reject the mudancas
                             *  then close the Mudancas
                             */
                            if ($sp->getAppSectorMan() == 2 && $sp->getAppSectorMan() != null) {
                                $mud->setDone('Feito');
                                $em->flush();
                            }
                        }

                        $areaResp =  $mud->getAreaResp();
                        $gestMudancas = $mud->getMangerMudancas();


                        // check which Form need 
                        $form = null;
                        if ($manager == true && $gestor == true) {
                            $form = $this->createForm(MudancasgestorType::class, $mud);
                        } elseif ($manager == true && $gestor != true) {
                            $form = $this->createForm(MudancasManagerType::class, $mud);
                        } elseif ($manager != true && $gestor == true) {
                            $form = $this->createForm(MudancasgestorType::class, $mud);
                        } else {
                            $form = $this->createForm(MudancasType::class, $mud);
                        }
                        // event listner 
                        $form->handleRequest($request);
                        //var of to check number of approved of area impacted
                        $NumberApproved = 0;


                        if ($mud->getAppMan() != null) {
                            if ($mud->getAppMan() == 2) {
                                $mud->setDone('Feito');
                                $em->flush();
                            }
                        }

                        if ($form->isSubmitted() && $form->isValid()) {
                            /**
                             * check if the person connect is manager 
                             * or gestor 
                             * or normal user
                             */
                            if ($manager && $gestor == false) {
                                /**
                                 * check manager approve 
                                 */
                                $mud->setAppMan($form["appMan"]->getData());
                                $em->persist($mud);
                                $em->flush();

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
                                    /**
                                     * fetech the sector process
                                     */
                                    foreach ($sps as $key => $sp) {
                                        foreach ($manager_dep as $key => $md) {
                                            if ($sp->getSector() == $md) {
                                                $sp->setComment($mud->getComMan());
                                                $sp->setAppSectorMan($mud->getAppMan());
                                                $em->persist($sp);
                                                $em->flush();
                                            }
                                        }
                                    }
                                } elseif ($mud->getAppMan() == 2) {
                                    /**
                                     * check if rejected 
                                     * so will reject for all the other area managed by the same person 
                                     */
                                    foreach ($sps as $key => $sp) {
                                        foreach ($manager_dep as $key => $md) {
                                            if ($sp->getSector() == $md) {
                                                $sp->setComment($mud->getComMan());
                                                $sp->setAppSectorMan($mud->getAppMan());
                                                $em->persist($sp);
                                                $em->flush();
                                            }
                                        }
                                    }
                                }


                                $em->persist($mud);
                                $em->flush();
                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                            } elseif ($gestor) {
                                $mud->setAreaResp($areaResp);
                                $mud->setMangerMudancas($gestMudancas);
                                $em->flush();
                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                            } else {
                            }
                        }

                        return $this->render('mudancas/update.html.twig', [
                            'controller_name' => 'Atualizar Mudancas',
                            'login' => 'null',
                            'creation' => 'false',
                            'person' => $person,
                            'm' => $mud,
                            'manager' => $manager,
                            'gestor' => $gestor,
                            'form' => $form->createView(),
                        ]);
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
                        'mud' => $mudancas,
                        'manager' => false,
                        'person' => $person,
                        'gestor' => false
                    ]);
                } else {
                    return $this->render('mudancas/history.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => $mudancas,
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

    public function sendEmail(ManagerRegistry $doctrine, Request $request, $email, $name, $mud, $per, $demand,  $gestor)
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
            $config->setSubject('Control de mudanças');
            $config->setChartSet('UTF-8');
            $em->persist($config);
            $em->flush();
        }

        $mail = new PHPMailer(true);
        // check the manager of the Mudancas 
        try {
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
            $mail->AddAddress($email, $name);
            $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
            $mail->CharSet = $config->getChartSet(); // Charset da mensagem (opcional)
            $mail->Subject  = $config->getSubject();
            $mail->msgHTML($this->renderView('emails/myemail.html.twig', [
                'name'  =>  'Control Mudanças',
                'mud'   =>  $mud,
                'per'   =>  $per,
                'name'  => $name,
                'gestor' => $gestor,
                'demand' =>  $demand
            ]));

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
