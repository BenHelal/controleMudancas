<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\ConfigEmail;
use App\Entity\DateTermine;
use App\Entity\Departemant;
use App\Entity\Email;
use App\Entity\EmailToSendConfig;
use App\Entity\Manager;
use App\Entity\Mudancas;
use App\Entity\MudancasSoftware;
use App\Entity\Person;
use App\Entity\Process;
use App\Entity\Projevisa;
use App\Entity\Requestper;
use App\Entity\Sector;
use App\Entity\SectorProcess;
use App\Entity\TokenData;
use App\Form\GerenteMudType;
use App\Form\MudancasgestorImpType;
use App\Form\MudancasgestorType;
use App\Form\MudancasManagerType;
use App\Form\MudancasgestorToAppType;
use App\Form\MudancasType;
use App\Model\Class\IpAdress;
use App\Model\Class\Logger;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\Validator\Constraints\Length;

/**
 * This method is the index action of the MudancasController.
 * It retrieves data from the database and renders the index view.
 *
 * @param ManagerRegistry $doctrine The Doctrine ManagerRegistry
 * @param Request $request The Symfony Request object
 * @return Response The Symfony Response object
 */
class MudancasController extends AbstractController
{

    #[Route('/changedate/{id}', name:'changeDate')]
    public function changeDate(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $dt = new DateTermine();
            $dt->setNewDateTime($mud->setEndMudancas());
            $dt->setOldDateTime($request->request->get('dateTime'));
            $dt->setJustification($request->request->get('justifications'));
            $dt->setMudancas($mud);
            $em->persist($dt);

            $mud->setEndMudancas($request->request->get('dateTime'));
            $mud->addDatesTermine($dt);
            
            $em->flush();
            return $this->redirectToRoute('upm', ['id' => $id]); 
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/errorUsing', name: "errorTest")]
    public function error(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            if ($req ==  null) {
                return $this->redirectToRoute('app_request');
            } else {
                return $this->render('error.html.twig', [
                    'controller_name' => 'timpelController',
                    'login' => 'false',
                    'person' => $person,
                    'index' => true,
                ]);
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas', name: 'app_mudancas')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        try {
            //code...
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
            $coordinatorArray = $em->getRepository(Sector::class)->findBy(['coordinator' => $person]);
            $managerArray = $em->getRepository(Sector::class)->findBy(['manager' => $person]);
            $mudanca = $em->getRepository(Mudancas::class)->findAll();

            foreach ($mudanca as $key => $mu) {
                # code...
                if($mu->getClient() != null){
                    $token = $em->getRepository(ApiToken::class)->findOneBy(['mud' => $mu]);
    
                    if($token != null){
                    $url = "10.100.2.61/ClientExteranlAcces/public/get/data";
                    //The data you want to send via POST
                    $fields = [
                        'token'=> $token->getToken(),
                        'id'=> $mu->getId(),
                    ];
        
                    //url-ify the data for the POST
                    $fields_string = http_build_query($fields);
        
                    //open connection
                    $ch = curl_init();
        
                    //set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        
                    //So that curl_exec returns the contents of the cURL; rather than echoing it
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
                    //execute post
                    $client = curl_exec($ch);
                    $cl = json_decode($client, true);
                    }else{
                        $cl = null;
                    }
                    $conn = $doctrine->getConnection();
                    try {
                        $appClt = $cl['mud']['TokenData']['appClt'];
                        if($appClt == "2"){
                            $isTheLastApprove = false;    
                            $mu->setImplemented(2);
                            $mu->setDone('Feito');
                            $em->flush();
                        }
                    } catch (\Throwable $th) {
                    }
                }
            }

            $mudancas = [];
            $gestorArray = $em->getRepository(Mudancas::class)->findBy(['mangerMudancas' => $person]);
            $notifGestor = [];
            
            $managerCorr = false;
            $corr = false;
            $manager = false ;
            $gestor = false ;

            if($managerArray != null & $coordinatorArray != null){
                //dd('i m manager and coor');
                $manager = true;
            }elseif($managerArray != null & $coordinatorArray == null){
                //dd('i m manager');
                $manager = true;
            }elseif($managerArray == null & $coordinatorArray != null){
                //dd('i m coor');
                $corr = true;
            }

            


            foreach ($gestorArray as $key => $value) {
                if ($value->getDone() != 'Feito'  && $value->getImplemented() == null ) {
                    array_push($notifGestor, $value);
                    $gestor = true;
                } elseif ($value->getDone() == 'Feito' && $value->getImplemented() == null) {
                    array_push($notifGestor, $value);
                    $gestor = true;
                }
            }

            //get the mudancas with the same Sector 
            if ($managerArray != null) {
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
                                    /*if ($val == $person->getFunction() && $value->getDone() != 'Feito' && $mudancas[sizeof($mudancas) - 1] != $value) {
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
                    }elseif($value->getManagerUserAdd() == $person && $value->getDone() != 'Feito'){
                        $manager = true;
                        array_push($mudancas, $value);
                    }elseif ($value->getAddBy()->getFunction() != null) {
                        if ($value->getAddBy()->getFunction()->getManager() == $person && $value->getImplemented() == null) {
                            array_push($mudancas, $value);
                        } 
                    } 
                }
            }


            $array = [];
            foreach ($mudanca as $key => $muda) {
                if ($muda->getAddBy() == $person && $muda->getImplemented() == null) {
                    array_push($array, $muda);
                }
                elseif ($muda->getAreaResp()->getManager() == $person  && $muda->getImplemented() == null) {
                    array_push($array, $muda);
                }
                elseif ($muda->getAddBy() != $person && $muda->getMangerMudancas() == $person  && $muda->getImplemented() == null) {
                    array_push($array, $muda);
                } elseif ($muda->getAddBy() != $person && $muda->getMangerMudancas() != $person && $muda->getManagerUserAdd() == $person  && $muda->getImplemented() == null) {
                    array_push($array, $muda);
                }  elseif ($muda->getAddBy()->getFunction()!= null) {
                      
                    if ($muda->getAddBy()->getFunction()->getManager() == $person && $muda->getImplemented() == null) {
                            array_push($array, $muda);
                    }else{
                        $areaImpact =  $muda->getAreaImpact();
                        $mangerArea = false;
                        foreach ($areaImpact as $key => $value) {
                            if ($value->getCoordinator() == $person ||  $value->getManager() == $person ) {
                                $mangerArea = true;
                                $manager = true;
                            }
                        }
                        if ($mangerArea  && $muda->getImplemented() == null) {
                            array_push($array, $muda);
                        }

                        else{
                        $dev = false;
                        if($muda->getMudS() != null){
                            foreach ($muda->getMudS()->getDevelopers() as $key => $value) {
                                if ($value == $person ) {
                                    $dev = true;
                                }
                            }
                            if ($dev && $muda->getImplemented() == null) {
                                array_push($array, $muda);
                            }
                        }


                    }
                    }
                } else {
                    $areaImpact =  $muda->getAreaImpact();
                    $mangerArea = false;
                    foreach ($areaImpact as $key => $value) {
                        if ($value->getCoordinator() == $person ||  $value->getManager() == $person ) {
                            $mangerArea = true;
                            $manager = true;
                        }
                    }
                    if ($mangerArea  && $muda->getImplemented() == null) {
                        array_push($array, $muda);
                    }else{
                        $dev = false;
                        if($muda->getMudS() != null){
                            foreach ($muda->getMudS()->getDevelopers() as $key => $value) {
                                if ($value == $person ) {
                                    $dev = true;
                                }
                            }
                            if ($dev && $muda->getImplemented() == null) {
                                array_push($array, $muda);
                            }
                        }


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
            foreach ($mudanca as $key => $muda) {
                foreach ($muda->getAreaImpact() as $key => $value) {
                    if ($value->getCoordinator() == $person ||  $value->getManager() == $person ) {
                    $mangerArea = true;
                    $manager = true;
                    }
                }
            }

            $array2 = [];
            $array3 = [];
            foreach ($array as $key => $value) {

                if($value->getMangerMudancas() == $person){
                  
                    if ($value->getDone() == null) {
                        if (!in_array($value, $array3)) {array_push($array3, $value);}
                        $gestor = true;
                    } elseif ($value->getDone() == 'Feito' && $value->getImplemented() == null) {
                        
                        if (!in_array($value, $array3)) {array_push($array3, $value);}
                        $gestor = true;
                    }  
                }

                if($value->getManagerUserApp() == null){
                    
                    if (!in_array($value, $array3)) {array_push($array3, $value);}
                }elseif($value->getAppMan() == null and $value->getAreaResp()->getManager() == $person  ){

                    
                    if (!in_array($value, $array3)) {array_push($array3, $value);}
                }
                    
            }  
            
            
            foreach ($array as $key => $value) { if($value->getManagerUserApp() == null){
                    
                if (!in_array($value, $array2)) {array_push($array2, $value);}
            }
                $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $value]);
                $oneOfSp = null;
                $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
                
                foreach ($sps as $sp) {
                    
                    if($sp->getAppSectorMan() == null && $sp->getPerson() == $person ){
                       
                        if (!in_array($value, $array2)) {array_push($array2, $value);}
                    }
                }
            }

            if ($req->getApproves() == 'yes') {
                if ($manager != true && $gestor == false) {
                   return $this->render('mudancas/index.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => array_reverse($array),
                        'manager' => false,
                        'person' => $person,
                        'gestor' => false
                    ]);
                } elseif ($gestor == true  && $manager == null) {
                    return $this->render('mudancas/index.html.twig', [
                        'controller_name' => 'Mudancas',
                        'login' => 'null',
                        'creation' => 'null',
                        'mud' => array_reverse($array),
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
                        'ln2' => $array2,
                        'ln3' => $array3,
                        'person' => $person
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_request');
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }
    } catch (\Throwable $th) {
     
        $logger = new Logger();
        $logger->log('Error add new CM', ['exception' => $th]);
        return $this->redirectToRoute('errorTest');
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
            $Was_send = 0;
            foreach ($mud->getAreaImpact() as $key => $value) {
                if ($value->getCoordinator() == $mud->getAddBy()) {
                    $Was_send++;
                    $email = new  Email();
                    $email->setMudancas($mud);
                    $email->setSendTo($value->getCoordinator());
                    $email->setSendBy($person);
                    $email->setTitle('Mudança implementada');
                    $email->setBody('gestorAppToArea');
                    $em->persist($email);
                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                }
            }
            if ($Was_send == 0) {
                $email = new  Email();
                $email->setMudancas($mud);
                $email->setSendTo($mud->getAddBy());
                $email->setSendBy($person);
                $email->setTitle('Mudança implementada');
                $email->setBody('gestorAppToUser');
                $em->persist($email);
                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
            }
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

            date_default_timezone_set("America/Sao_Paulo");
            $time = new \DateTime();

            $mud->setDateMUA($time);
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
                            if ($value->getCoordinator() == $person & $value->getManager() == $person) {
                                array_push($area, $value);
                            }
                        }
                        $form = $this->createForm(GerenteMudType::class, $mud);
                        $form->handleRequest($request);

                        if ($form->isSubmitted()) {

                            if ($mud->getManagerUserApp() == 2) {

                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();

                                $mud->setDateMUA($time);

                                $mud->setImplemented(2);
                                $mud->setDone('Feito');
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle('reject gerente controle do mudancas');
                                $email->setBody('manager1Rej');
                                $em->persist($email);
                                $em->flush();
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                            } else {
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();

                                $mud->setDateMUA($time);
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle('approved gerente controle do mudancas');
                                $email->setBody('manager1APP');
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

                                /*foreach ($mud->getAreaImpact() as $key => $value) {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($value->getCoordinator());
                                    $email->setSendBy($person);
                                    $email->setTitle('ÁREA IMPACTADA');
                                    $email->setBody('managerArea');
                                    $em->persist($email);
                                    $em->flush();
                                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                }*/
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

                        $projevisaUser = $em->getRepository(Projevisa::class)->find(1);
                        $projevisa = isset($_POST['Projevisa']) ? 1 : 0;
                        
                        if($projevisa ){
                            
                            if ($projevisaUser->getUser() != null) {

                                $this->sendEmail($doctrine, $request, $projevisaUser->getUser(), $mud,$person, "projevisa", false);
                            
                            }
                        }

                        if ($mud->getAddBy() == $person && $mud->getAddBy()->getFunction()->getManager() == $person) {
                            date_default_timezone_set("America/Sao_Paulo");
                            $time = new \DateTime();
                            $time->format('Y-m-d H:i:s');
                            $mud->setDateMUA($time);
                            //nansen
                           /* $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($person);
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação automática da solicitação');
                            $email->setBody('nansenAddBy');
                            $em->persist($email);*/

                            //nansen
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($person->getFunction()->getManager());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação automática da solicitação');
                            $email->setBody('manager1');
                            $em->persist($email);
                            
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($mud->getAddBy());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação gerente do solicitante');
                            $email->setBody('manager1APP');
                            $em->persist($email);

                        }  else {
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                $time->format('Y-m-d H:i:s');
                                $mud->setDateMUA($time);
                                //nansen
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($person->getFunction()->getManager());
                                $email->setSendBy($person);
                                $email->setTitle('Aprovação automática da solicitação');
                                $email->setBody('manager1');
                                $em->persist($email);
    
                            }

                            $em->flush();
                      //  }
                        $sec = $em->getRepository(Sector::class)->findBy(['manager' => $person]);
                        foreach ($sec as $key => $value) {
                            if ($value === $mud->getAreaResp()) {
                                $mud->setApproved('approved');
                                $manager = true;
                            }
                        }
                        $em->persist($mud);
                        $emails = $em->getRepository(Email::class)->findBy(['mudancas' => $mud]);
                        $ems = [];
                        foreach ($emails as $key => $value) {
                            if ($value->getClient() == null) {
                                $this->sendEmail($doctrine, $request, $value->getSendTo(), $value->getMudancas(), $value->getSendBy(), $value->getBody(), false);
                            } 
                        }
                        if ($mud->getAddBy() == $mud->getAddBy()->getFunction()->getManager()) {
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
                                $SectorProcess->setPerson($value->getCoordinator());
                                $em->persist($SectorProcess);
                                $em->flush();
                            }

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
                                $SectorProcess->setPerson($value->getCoordinator());
                                $em->persist($SectorProcess);
                                $em->flush();
                            }
                            if ($manager) {
                                return $this->redirectToRoute('approve', ['id' => $mud->getId()]);
                            }
                        }
                        $em->flush();
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

    #[Route('/createMudancas/soft', name: 'cmSoft')]
    public function createSoft(ManagerRegistry $doctrine, Request $request): Response
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
                    $mud->setTypeMud("1");
                    //create new Mudancas software 
                    $ms = new MudancasSoftware();
                    $em->persist($ms);
                    $areaResp = $em->getRepository(Sector::class)->findOneBy(['name' => '021 – TI MATRIZ (INFRAESTRUTURA E REDE)']);
                    $mud->setAreaResp($areaResp);
                    $mud->setMudS($ms);
                   // dd($emailConfigSoftware);

                   //for refrence  to the process in mudanças
                    $all_mudancas = $em->getRepository(Mudancas::class)->findAll();

                    $form = $this->createForm(MudancasType::class, $mud);
                    $form->handleRequest($request);

                    if ($form->isSubmitted()) {
                        $allValues = $request->request->all();
                        $ref = $allValues['mudancas']['ref'];
                        if($ref != ""){
                            $mudRef = $em->getRepository(Mudancas::class)->find($ref);
                            $ms->setReference($mudRef);
                        }
                        $email = new  Email();
                        $email->setMudancas($mud);
                        $email->setSendTo($person);
                        $email->setSendBy($person);
                        $email->setTitle('Novo mudancas');
                        $email->setBody('create');
                        $em->persist($email);

                            if ($mud->getAddBy() == $person && $mud->getAddBy()->getFunction()->getManager() == $person) {
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                $time->format('Y-m-d H:i:s');
                                $mud->setDateMUA($time);
                                
                                
                            //nansen
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($person->getFunction()->getManager());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação automática da solicitação');
                            $email->setBody('manager1');
                            $em->persist($email);
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($mud->getAddBy());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação gerente do solicitante');
                            $email->setBody('manager1APP');
                            $em->persist($email);



                            } else {
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                $time->format('Y-m-d H:i:s');
                                $mud->setDateMUA($time);
                                
                                
                            //nansen
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($person->getFunction()->getManager());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovação automática da solicitação');
                            $email->setBody('manager1');
                            $em->persist($email);

                            }

                            $em->flush();
                       // }
                        $sec = $em->getRepository(Sector::class)->findBy(['manager' => $person]);
                        foreach ($sec as $key => $value) {
                            if ($value === $mud->getAreaResp()) {
                                $mud->setApproved('approved');
                                $manager = true;
                            }
                        }
                        $em->persist($mud);
                        
                        $emails = $em->getRepository(Email::class)->findBy(['mudancas' => $mud]);
                        $ems = [];
                        foreach ($emails as $key => $value) {
                            if ($value->getClient() == null) {
                                $this->sendEmail($doctrine, $request, $value->getSendTo(), $value->getMudancas(), $value->getSendBy(), $value->getBody(), false);
                            } 
                        }
                        
                        $nansenName =  $form["nansenName"]->getData();
                        
                        if ($mud->getAddBy() == $mud->getAddBy()->getFunction()->getManager()) {
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
                                $SectorProcess->setPerson($value->getCoordinator());
                                $em->persist($SectorProcess);
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
                                $SectorProcess->setPerson($value->getCoordinator());
                                $em->persist($SectorProcess);
                            }
                            $em->flush();
                            if ($manager) {
                                return $this->redirectToRoute('approve', ['id' => $mud->getId()]);
                            }
                        }
                        $em->flush();

                        return $this->redirectToRoute('app_mudancas');
                    }
                    return $this->render('mudancas/update.html.twig', [
                        'controller_name' => 'Atualizar Mudancas',
                        'login' => 'null',
                        'creation' => 'true',
                        'gestor' => false,
                        'form' => $form->createView(),
                        'person' => $person,
                        'all_mudancas' => $all_mudancas,
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


    #[Route('/createMudancas/migrateSoft/{id}', name: 'migrateSoft')]
    public function migrateSoft(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
             $mud = $em->getRepository(Mudancas::class)->find($id);
            if($mud->getAreaResp()->getName() == "021 – TI MATRIZ (INFRAESTRUTURA E REDE)" && $mud->getAreaResp()->getManager() == $person){
                $mud->setTypeMud("1");
             //create new Mudancas software 
             $ms = new MudancasSoftware();
             $em->persist($ms);
             $mud->setMudS($ms);
             $em->flush();
            }
                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
       
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
            $token = $em->getRepository(ApiToken::class)->findOneBy(['mud' => $mud]);
            if ($token != null) {
                //143.255.163.142
                //10.100.2.61
                $url = "10.100.2.61/ClientExteranlAcces/public/get/data";
                //The data you want to send via POST
                $fields = [
                    'token' => $token->getToken(),
                    'id' => $mud->getId(),
                ];

                //url-ify the data for the POST
                $fields_string = http_build_query($fields);

                //open connection
                $ch = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

                //So that curl_exec returns the contents of the cURL; rather than echoing it
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                //execute post
                $client = curl_exec($ch);
                $cl = json_decode($client, true);

                if ($cl["mud"]["TokenData"]["comClt"] != null) {
                    if ($cl["mud"]["TokenData"]["appClt"] == 2) {
                        $mud->setImplemented(2);
                        if ($mud->getNansenNumber() != null) {

                            $mud->setManagerUserAdd($person);
                        }
                    }
                
                    $mud->setdescClient($cl["mud"]["TokenData"]["comClt"]);
                }
            } else {
                $cl = null;
            }


            // condition on  situation of Mudanacas
            if ($mud == null) {
                return $this->redirectToRoute('app_mudancas');
            } else {

                if($mud->getMangerMudancas() != null){
                    
                if($mud->getMudS()!= null){
                    return $this->redirectToRoute('Softindex', ['id' => $mud->getId()]);
                }
            }

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
                            $done = true;
                            $mud->setDone('Feito');
                        }
                        $em->persist($mud);
                        $em->flush();
                    }
                }
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
                        $mangerOfAreaDidntApp = false;
                        //fetch the sectorPress 
                        foreach ($sps as $key => $sp) {
                            /**
                             *  Check if there is one of the manager reject the mudancas
                             *  then close the Mudancas
                             */
                            if ($sp->getAppSectorMan() == null & $sp->getComment() != 'validar pelo Código Nansen') {
                                $mangerOfAreaDidntApp = true;
                            }
                        }

                        $areaResp =  $mud->getAreaResp();
                        $gestMudancas = $mud->getMangerMudancas();
                        if ($mud->getMangerMudancas() != null) {
                            if ($mud->getMangerMudancas()->getId() == $person->getId()) {
                                $gestor = true;

                                if ($mud->getStartMudancas() != null) {
                                    $date1 = $mud->getStartMudancas();
                                } else {
                                    $mud->setStartMudancas((new \DateTime())->format('d-m-Y'));
                                    $date1 = $mud->getStartMudancas();
                                }
                                if ($mud->getEndMudancas() != null) {
                                    $date2 = $mud->getEndMudancas();
                                } else {
                                    $mud->setEndMudancas((new \DateTime())->format('d-m-Y'));
                                    $date2 = $mud->getEndMudancas();
                                }
                                if ($mud->getEffictiveStartDate() != null) {
                                    $date3 = $mud->getEffictiveStartDate();
                                } else {
                                    $mud->setEffictiveStartDate((new \DateTime())->format('d-m-Y'));
                                    $date3 = $mud->getEffictiveStartDate();
                                }
                            }
                        }
                        // check which Form need 

                        if($mud->getManagerUserApp() == null){
                            $manager = false;
                        }

                        $form = null;
                        if ($manager == true && $gestor == false && $mangerOfAreaDidntApp == false) {
                            $form = $this->createForm(MudancasManagerType::class, $mud);
                        } elseif ($gestor == true && $mangerOfAreaDidntApp == false) {
                            $form = $this->createForm(MudancasgestorToAppType::class, $mud);
                            $formImp = $this->createForm(MudancasgestorImpType::class, $mud);
                        } elseif ($gestor == true && $mangerOfAreaDidntApp == false) {
                            $form = $this->createForm(MudancasgestorToAppType::class, $mud);
                            $formImp = $this->createForm(MudancasgestorImpType::class, $mud);
                        } elseif ($manager == true && $gestor != true) {
                            $form = $this->createForm(MudancasManagerType::class, $mud);
                        } elseif ($gestor == true && $mangerOfAreaDidntApp == true && $date1 != null) {
                            $form = $this->createForm(MudancasgestorType::class, $mud);
                        } elseif ($gestor == true && $mangerOfAreaDidntApp == true && $date1 == null) {
                            $form = $this->createForm(MudancasgestorType::class, $mud);
                        } else {
                            $form = $this->createForm(MudancasType::class, $mud);
                        }
                        $form->handleRequest($request);
                        $NumberApproved = 0;
                        if ($mud->getAppMan() != null) {
                            if ($mud->getAppMan() == 2) {
                                $mud->setImplemented(2);
                                $mud->setDone('Feito');
                                $em->flush();
                            }
                        }
                        if ($gestor == true && $mangerOfAreaDidntApp == false) {
                            if ($mud->getTypeMud() == 1) {
                                return $this->redirectToRoute('solfirst_software', ['id' => $mud->getId()]);
                            }
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

                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                $time->format('Y-m-d H:i:s');
                                $mud->setDateOfImp($time);

                                $em->flush();
                                if ($mud->getImplemented() == 1) {
                                    return $this->redirectToRoute('imp', ['id' => $mud->getId()]);
                                } elseif ($mud->getImplemented() == 2) {
                                    return $this->redirectToRoute('impnoa', ['id' => $mud->getId()]);
                                }
                            }
                        }


                        if ($form->isSubmitted() && $form->isValid()) {
                            $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mud]);
                            $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
    
                            if ($manager && $gestor == false) {
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                if($mud->getDateAM() != null){
                                    foreach ($sps as $key => $value) {
                                        if ($value->getPerson() == $person) {
                                            if($value->getDataCreation() == null){
                                                
                                                $dateTime = \DateTime::createFromFormat('Y-m-d H:i', $mud->getDateAM());
                                                try {
                                                    $dateTime = new DateTimeImmutable($mud->getDateAM());
                                                    $value->setDataCreation($dateTime);
                                                } catch (Exception $e) {
                                                    // Handle parsing error (e.g., throw exception)
                                                }
                                                $value->setDataCreation($dateTime);
                                                $value->setAppSectorMan($form["appMan"]->getData());
                                                $value->setComment($form["comMan"]->getData());
                                                $em->flush();
                                            }
                                        }
                                    } 
                                }else{
                                    $mud->setDateAM($time);
                                    $mud->setAppMan($form["appMan"]->getData());
                                    $mud->setComMan($form["comMan"]->getData());
                                    $em->persist($mud);
                                    $em->flush();
                                    $number_sector_app = 0;
                                    foreach ($sps as $key => $value) {
                                            if ($value->getPerson() == $person) {if($value->getDataCreation() == null){
                                                date_default_timezone_set("America/Sao_Paulo");
                                                $time = new \DateTime();
                                                $value->setDataCreation($time);
                                                $value->setAppSectorMan($form["appMan"]->getData());
                                                $value->setComment($form["comMan"]->getData());
                                                $em->flush();
                                            }
                                        }
                                    }
                                    $manager_dep = [];
                                    $areaImpact =  $mud->getAreaImpact();
                                    foreach ($areaImpact as $key => $ai) {
                                        if ($ai->getManager() == $person) {
                                            array_push($manager_dep, $ai);
                                        }
                                    }
                                    if ($mud->getAppMan() == 1) {
                                    if ($mud->getClient() != null) {
                                        if ($mud->getClient()->getRespEmail() != null) {
                                            $token = new ApiToken($mud->getClient(), $mud);
                                            $em->persist($token);
                                            $tok = new TokenData();
                                            $tok->setMudancas($mud->getId());
                                            $tok->setNomeMudanca($mud->getNomeMudanca());
                                            $tok->setDescMudanca($mud->getDescMudanca());
                                            $tok->setDescImpacto($mud->getDescImpacto());
                                            $tok->setDescImpactoArea($mud->getDescImpactoArea());
                                            $tok->setJustif($mud->getJustif());
                                            $tok->setComMan($mud->getComMan());
                                            $tok->setAppMan($mud->getAppMan());
                                            $tok->setToken($token->getToken());
                                            $tok->setResp($mud->getClient()->getResp());
                                            $tok->setRespEmail($mud->getClient()->getRespEmail());
                                            $email = new  Email();
                                            $email->setMudancas($mud);
                                            $email->setClient($mud->getClient());
                                            $email->setSendBy($person);
                                            $email->setTitle('Aprovação Client');
                                            $email->setBody('client');
                                            $em->persist($email);
                                            $em->persist($tok);
                                            $em->flush();
                                            if ($token != null) {
                                                $url = "10.100.2.61/ClientExteranlAcces/public/add/data/token";
                                                $fields = [
                                                    'token' =>         $token->getToken(),
                                                    'mudancas' =>      $mud->getId(),
                                                    'nomeMud' =>       $mud->getNomeMudanca(),
                                                    'descMud' =>       $mud->getDescMudanca(),
                                                    'descImpacto' =>   $mud->getDescImpacto(),
                                                    'descImpactoArea' => $mud->getDescImpactoArea(),
                                                    'justif' =>        $mud->getJustif(),
                                                    'comMan' =>        $mud->getComMan(),
                                                    'appMan' =>        $mud->getAppMan(),
                                                    'token' =>         $token->getToken(),
                                                    'resp' =>          $mud->getClient()->getResp(),
                                                    'respEmail' =>     $mud->getClient()->getRespEmail(),
                                                ];
                                                $fields_string = http_build_query($fields);
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $url);
                                                curl_setopt($ch, CURLOPT_POST, true);
                                                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                $client = curl_exec($ch);
                                                $cl = json_decode($client, true);
                                            } else {
                                                $cl = null;
                                            }
                                            //$this->sendEmail($doctrine, $request, $mud->getClient(), $mud, $person, 'client', false, $mud->getClient());
                                        }
                                    }
                                    $emails = $em->getRepository(Email::class)->findBy(['mudancas' => $mud]);
                                    $ems = [];
                                    foreach ($ems as $key => $value) {
                                        //$email = new Email();
                                        $email = $value;
                                        $em->persist($email);
                                        if ($value->getSendTo() != $value->getSendBy()) {
                                            $this->sendEmail($doctrine, $request, $value->getSendTo(), $value->getMudancas(), $value->getSendBy(), $value->getBody(), false);
                                        }
                                    }

                                    /*if ($mud->getNansenNumber() == null) {
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($mud->getAddBy());
                                        $email->setSendBy($person);
                                        $email->setTitle('Aprovado pelo Gerente do solicitante');
                                        $email->setBody('gerenteAPP');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    *///} else {
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($mud->getAddBy());
                                        $email->setSendBy($person);
                                        $email->setTitle('Aprovado pelo Gerente do solicitante');
                                        $email->setBody('gerenteAPP');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    //}
                                    foreach ($mud->getAreaImpact() as $key => $value) {
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($value->getCoordinator());
                                        $email->setSendBy($person);
                                        $email->setTitle('ÁREA IMPACTADA');
                                        $email->setBody('managerArea');
                                        $em->persist($email);
                                           $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    }
                                    foreach ($sps as $key => $sp) {
                                        foreach ($manager_dep as $key => $md) {
                                            if ($sp->getSector() == $md & $sp->getPerson() == $person) {
                                                date_default_timezone_set("America/Sao_Paulo");
                                                $time = new \DateTime();
                                                $sp->setDataCreation($time);
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
                                        $email->setTitle('Notificação para o gestor da mudança');
                                        $email->setBody('gestor');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                   
                                        if($mud->getTypeMud() == '1'){
                                          if($mud->getMudS()->getIniciar() == null && $mud->getMangerMudancas() != null ){
                                            $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '2']);
                                            $email = new  Email();
                                            $email->setMudancas($mud);
                                            $email->setSendTo($mud->getMangerMudancas());
                                            $email->setSendBy($person);
                                            $email->setTitle($emailConfigSoftware->getSubjectMessage());
                                            $email->setBody($emailConfigSoftware->getTitleOfMessage());
                                            $em->persist($email);
                                            $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false,);
                                            }
                                        }

                                    }
                                    } elseif ($mud->getAppMan() == 2) {
                                    $mud->setImplemented(2);
                                    if ($mud->getNansenNumber() == null) {
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($mud->getMangerMudancas());
                                        $email->setSendBy($person);
                                        $email->setTitle('Reprovado pelo Gerente do solicitante');
                                        $email->setBody('gerenteREP');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    } else {
                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($mud->getAddBy());
                                        $email->setSendBy($person);
                                        $email->setTitle('Aprovado pelo Gerente do solicitante');
                                        $email->setBody('gerenteREP');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    }
                                    if ($mud->getMangerMudancas() != $mud->getAddBy()) {

                                        $email = new  Email();
                                        $email->setMudancas($mud);
                                        $email->setSendTo($mud->getAddBy());
                                        $email->setSendBy($person);
                                        $email->setTitle('Reprovado pelo Gerente do solicitante');
                                        $email->setBody('gerenteREP');
                                        $em->persist($email);
                                        $em->flush();
                                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                                    }
                                    foreach ($sps as $key => $sp) {
                                        foreach ($manager_dep as $key => $md) {
                                            if ($sp->getSector() == $md & $sp->getPerson() == $person) {

                                                date_default_timezone_set("America/Sao_Paulo");
                                                $time = new \DateTime();

                                                $sp->setDataCreation($time);
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

                                    return $this->redirectToRoute('upm', ['id' => $mud->getId()]);

                                }
                            } elseif ($gestor) {

                                $mud->setAreaResp($areaResp);
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                $mud->setDateAG($time);
                                $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mud]);
                                $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
                                $number_sector_app = 0;
                                foreach ($sps as $key => $value) {
                                    if ($value->getAppSectorMan() != null) {
                                        $number_sector_app++;
                                    };
                                }
                                if ($mud->getAppGest() == 1) {
                                    $email = new  Email();
                                    $email->setMudancas($mud);
                                    $email->setSendTo($mud->getAddBy());
                                    $email->setSendBy($person);
                                    $email->setTitle('Aprovado pelo gerente da mudança');
                                    $email->setBody('approved_ger');
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
                                    $mud->setImplemented(2);
                                    $mud->setDone('Feito');
                                    $mud->setAreaResp($areaResp);
                                    $em->persist($mud);
                                    $em->flush();
                                }
                                $mangerOfAreaDidntApp = false;
                                foreach ($sps as $key => $sp) {
                                    if ($sp->getAppSectorMan() == 2 && $sp->getAppSectorMan() != null) {
                                        $mud->setImplemented(2);
                                        $mud->setDone('Feito');
                                    } elseif ($sp->getAppSectorMan() == null) {
                                    }
                                }
                                $mud->setMangerMudancas($gestMudancas);
                                $mud->setEndMudancas($form["endMudancas"]->getData());
                                $mud->setStartMudancas($form["startMudancas"]->getData());
                                $mud->setEffictiveStartDate($form["effictiveStartDate"]->getData());
                                $em->persist($mud);
                                $em->flush();
                                return $this->redirectToRoute('upm', ['id' => $mud->getId()]);
                            } else {
                            }
                        }
                        $email = false;
                        if ($mud->getClient() != null) {
                            if ($mud->getClient()->getRespEmail() != null) {
                                $email = true;
                            }
                        }
                        if ($gestor == true && $mangerOfAreaDidntApp == false) {
                            return $this->render('mudancas/update.html.twig', [
                                'controller_name' => 'Atualizar Mudancas',
                                'login' => 'null',
                                'creation' => 'false',
                                'person' => $person,
                                'm' => $mud,
                                'email' => $email,
                                'mangerOfAreaDidntApp' => $mangerOfAreaDidntApp,
                                'manager' => $manager,
                                'gestor' => $gestor,
                                'cl' => $cl,
                                'date1' => $date1,
                                'date2' => $date2,
                                'date3' => $date3,
                                'formImp' => $formImp->createView(),
                                'form' => $form->createView(),
                            ]);
                        } elseif ($gestor == true && $mangerOfAreaDidntApp == true) {
                            return $this->render('mudancas/update.html.twig', [
                                'controller_name' => 'Atualizar Mudancas',
                                'login' => 'null',
                                'creation' => 'false',
                                'person' => $person,
                                'm' => $mud,
                                'email' => $email,
                                'mangerOfAreaDidntApp' => $mangerOfAreaDidntApp,
                                'manager' => $manager,
                                'gestor' => $gestor,
                                'cl' => $cl,
                                'date1' => $date1,
                                'date2' => $date2,
                                'date3' => $date3,
                                'form' => $form->createView(),
                            ]);
                        } else {
                            return $this->render('mudancas/update.html.twig', [
                                'controller_name' => 'Atualizar Mudancas',
                                'login' => 'null',
                                'creation' => 'false',
                                'person' => $person,
                                'm' => $mud,
                                'email' => $email,
                                'mangerOfAreaDidntApp' => $mangerOfAreaDidntApp,
                                'manager' => $manager,
                                'gestor' => $gestor,
                                'date1' => '',
                                'date2' => '',
                                'date3' => '',
                                'cl' => $cl,
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
            $manager = null;
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

                $ApiToken = $em->getRepository(ApiToken::class)->findOneBy(['mud' => $mud->getId()]);
                //dd($ApiToken);
                $mail->AddAddress($client->getRespEmail(), $client->getResp());
                $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
                $mail->CharSet = $config->getChartSet(); // Charset da mensagem (opcional)
                $mail->Subject  = $config->getSubject();
                $mail->msgHTML($this->renderView('emails/myemail.html.twig', [
                    'name'      =>  'Controle de Mudanças',
                    'mud'       =>  $mud,
                    'sendTo'    => $config->getEmailSystem(),
                    'per'       =>  $per,
                    'c'         => $client,
                    'token' => $ApiToken,
                    'ip'        => $ipAdress->getIpAdress(),
                    'name'      => $client->getResp(),
                    'gestor'    => $gestor,
                    'demand'    =>  $demand
                ]));
            } else {
                
                if($demand == "projevisa"){
                    
                $mail->AddAddress($sendTo, $sendTo);
                
                $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
                $mail->CharSet = $config->getChartSet(); // Charset da mensagem (opcional)
                $mail->Subject  = $config->getSubject();
                $mail->msgHTML($this->renderView('emails/myemail.html.twig', [
                    'name'      =>  'Controle de Mudanças',
                    'mud'       =>  $mud,
                    'sendTo'    => $sendTo,
                    'per'       =>  $per,
                    'ip'        => $ipAdress->getIpAdress(),
                    'name'      => $sendTo,
                    'gestor'    => $gestor,
                    'demand'    =>  $demand
                ]));
                }else{
                    
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

    #[Route('/Admin.xlsx', name:'exportFile')]
    public function getExportResultFile(ManagerRegistry $doctrine, Request $request){
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();

            return $this->redirectToRoute('export');
           
        }
    }

    #[Route('/sobre', name:'about')]
    public function about(ManagerRegistry $doctrine,Request $request){
        
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            //dd($session->get('name'));
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $branch = trim(shell_exec('git log -1'));
            
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
            $listrequest = $em->getRepository(Requestper::class)->findAll();    
            $val = intval($this->presentNotDone($val, $val2));
            $size = sizeof($mudancas);

            $mudSoft = [];
            $mudNorm = [];
            foreach ($mudancas as $key => $value) {
                if($value->getMudS() != null ){
                    array_push($mudSoft,$value);
                }else{
                    
                    array_push($mudNorm,$value);
                }
            }

            
            preg_match('/^commit [0-9a-f]{40}$/m', $branch, $matches);
            $commitHash = trim($matches[0]);
            $branch = trim(shell_exec(" git name-rev --name-only HEAD"));
            $branch = substr($branch, strrpos($branch, '/') + 1);
       if($branch == 'local' | $branch == 'local2'| $branch == 'local4' | $branch == 'local5'){    
            $date =trim(shell_exec("git log -1 --format='%cd' --date=format:'%d/%m/%Y' ")); 
            $version =  $branch[0].'_1.'.$branch[strlen($branch)-1];
        }elseif($branch == 'prod'| $branch == 'prod1' | $branch == 'prod2'| $branch == 'prod3'| $branch == 'prod4'){       
            $date =trim(shell_exec("git log -1 --format='%cd' --date=format:'%d/%m/%Y' ")); 
            $version =  $branch[0].'_1.'.$branch[strlen($branch)-1];
        }elseif($branch == 'test9' | $branch == 'test2'  | $branch == 'test4' | $branch == 'test5'| $branch == 'test6'){
            $date =trim(shell_exec("git log -1 --format='%cd' --date=format:'%d/%m/%Y'")); 
            $version =  $branch[0].'_1.'.$branch[strlen($branch)-1];  
        }else{        
            $date =trim(shell_exec("git log -1 --format='%cd' --date=format:'%d/%m/%Y' ")); 
            $version =  $branch[0].'_1.9';
        }   

        return $this->render('software/about.html.twig', [
            'controller_name' => 'Mudancas',
            'login' => 'null',
            'creation' => 'null',
            'person' => $person,
            'version' => $version,
            'date' => $date,
            
            'percent' => $val,
            'size' => $size,
            'sizeSoft' => sizeof( $mudSoft),
            'sizeNorm' => sizeof( $mudNorm),
        ]);
    } else {
        return $this->redirectToRoute('log_employer');
    }
}
}
