<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\ConfigEmail;
use App\Entity\Email;
use App\Entity\EmailToSendConfig;
use App\Entity\Mudancas;
use App\Entity\MudancasSoftware;
use App\Entity\Person;
use App\Entity\Process;
use App\Entity\Requestper;
use App\Entity\SectorProcess;
use App\Entity\StepsDev;
use App\Entity\StepsGestor;
use App\Entity\StepsTest;
use App\Entity\StepsTestSol;
use App\Entity\TokenData;
use App\Form\MudancasGestorSoftwareAppType;
use App\Form\MudancasGestorSoftwareType;
use App\Form\MudancasSoftgestorImpType;
use App\Form\MudancasSoftwareDevsType;
use App\Form\MudancasSoftwareDevType;
use App\Form\MudancasSoftwareGerenteDeAprovacaoType;
use App\Form\MudancasSoftwareSolicitanteType;
use App\Form\MudancasSoftwareSolRefType;
use App\Form\MudancasSoftwareTestersTiType;
use App\Form\MudancasSoftwareTestersType;
use App\Form\MudancasSoftwareTestesSolType;
use App\Form\MudancasSoftwareTestesTiType;
use App\Form\MudancasSoftwareType;
use App\Form\MudancasType;
use App\Model\Class\IpAdress;
use Doctrine\Persistence\ManagerRegistry;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MudancasSoftwareController extends AbstractController
{

    #[Route('/mudancas/software/{id}', name: 'Softindex')]
    public function index(ManagerRegistry $doctrine, Request $request, $id)
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

            $mudSoft = $mud->getMudS();

                # code...
                $form = $this->createForm(MudancasType::class, $mud);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                }

                return $this->render('mudancas_software/index.html.twig', [
                    'controller_name' => 'MudancasSoftwareController',
                    'login' => 'null',
                    'creation' => 'false',
                    'form' => $form->createView(),
                    'm' => $mud,
                    'muds' => $mudSoft,
                    'person' => $person,
                ]);
            
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/order/mud/', name:'orderMudancas')]
    public function orderMudanc(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $allMudancas = $em->getRepository(Mudancas::class)->findByType("1");
            
            $array = [];
            foreach ($allMudancas as $key => $value) {
                # code...
                            /**
             * get the Process with mudancas 
             * to can get The Sector 
             * and check the situation of the mudancas  
             */
            $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $value]);

            /**
             * get the List of SectorProcess 
             * to can access to the sector 
             */
            $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
            $mudSoft = $value->getMudS();


            $areaImpactadaDidntApp = false;
            //fetch the sectorPress 
            foreach ($sps as $key => $sp) {
                /**
                 *  Check if there is one of the manager reject the mudancas
                 *  then close the Mudancas
                 */
                if ($sp->getAppSectorMan() == null ) {
                    $areaImpactadaDidntApp = true;
                }
            }

            if ($areaImpactadaDidntApp == false) {
                array_push($array, $value);
            }
            }

            return $this->render('mudancas_software/changeOrderView.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'm' => $array,
                'person' => $person,
            ]);
        }else{
            return $this->redirectToRoute('log_employer');
        }
    }

    
    #[Route('/updateorder', name: 'updateorder', methods:["POST"])]
    public function updateOrder(ManagerRegistry $doctrine, Request $request)
    { 
        
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            
        $orderData = json_decode($request->request->get('orderData'), true);

        $em = $doctrine->getManager();

        foreach ($orderData as $data) {
            $mud = $em->getRepository(Mudancas::class)->find($data['id']);
            $mud->setOrderNumber($data['order']);



            $em->flush();
        }


        return new JsonResponse(['status' => 'success']); }else{
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas/software/solfirst/Aprocao/{id}', name: 'solfirst_software')]
    public function solfirst(ManagerRegistry $doctrine, Request $request, $id)
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

            $mudSoft = $mud->getMudS();
           $mudSoft->setGestor($mud->getMangerMudancas());
            if (($mud->getAddBy() == $person & $mudSoft->getRef() != null) || $mud->getAddBy() != $person) {
                # code...
                return $this->redirectToRoute('Softindex', ['id' => $mud->getId()]);
            } else {
                # code...
                $form = $this->createForm(MudancasSoftwareSolRefType::class, $mudSoft);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $em->flush();
                    return $this->redirectToRoute('Softindex', ['id' => $mud->getId()]);
                }

                return $this->render('mudancas_software/solFirst_software.html.twig', [
                    'controller_name' => 'MudancasSoftwareController',
                    'login' => 'null',
                    'creation' => 'false',
                    'form' => $form->createView(),
                    'person' => $person,
                    'm' => $mud,
                ]);
            }
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
            $mudSoft = $mud->getMudS();


            $areaImpactadaDidntApp = false;
            //fetch the sectorPress 
            foreach ($sps as $key => $sp) {
                /**
                 *  Check if there is one of the manager reject the mudancas
                 *  then close the Mudancas
                 */
                if ($sp->getAppSectorMan() == null & $sp->getComment() != 'validar pelo Código Nansen') {
                    $areaImpactadaDidntApp = true;
                }
            }

            $manager = false;
            if ($person == $mud->getMangerMudancas()) {
                $manager = true;
            }


            $formImp = $this->createForm(MudancasSoftgestorImpType::class, $mud);
            $formImp->handleRequest($request);
            if ($formImp->isSubmitted()) {

                $desImp = $mud->getImpDesc();
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

            $formTesters = $this->createForm(MudancasSoftwareTestersType::class, $mudSoft);
            $formTesters->handleRequest($request);

            if ($formTesters->isSubmitted() && $formTesters->isValid()) {
                $testers = $mudSoft->getTesters();
                foreach ($testers as $key => $value) {
                    $email = new  Email();
                    $email->setMudancas($mud);
                    $email->setSendTo($value);
                    $email->setSendBy($person);
                    $email->setTitle('Gestor adiciona você como Testador de cliente ');
                    $email->setBody('testSol');
                    $em->persist($email);

                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                    $em->flush();
                }
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }

            $formTestersTi = $this->createForm(MudancasSoftwareTestersTiType::class, $mudSoft);
            $formTestersTi->handleRequest($request);

            if ($formTestersTi->isSubmitted() && $formTestersTi->isValid()) {
                $testTi = $mudSoft->getTestersti();
                foreach ($testTi as $key => $value) {
                    # code...
                    $email = new  Email();
                    $email->setMudancas($mud);
                    $email->setSendTo($value);
                    $email->setSendBy($person);
                    $email->setTitle('Gestor adiciona você como Testador Ti');
                    $email->setBody('testTi');
                    $em->persist($email);

                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                    $em->flush();
                }
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }

            $formDevs = $this->createForm(MudancasSoftwareDevsType::class, $mudSoft);
            $formDevs->handleRequest($request);

            if ($formDevs->isSubmitted() && $formDevs->isValid()) {
                $developers = $mudSoft->getDevelopers();
                foreach ($developers as $key => $value) {
                    $email = new  Email();
                    $email->setMudancas($mud);
                    $email->setSendTo($value);
                    $email->setSendBy($person);
                    $email->setTitle('Gestor adiciona você como Desenvolvedor ');
                    $email->setBody('developer');
                    $em->persist($email);
                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);

                    $em->flush();
                }
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }

            if ($areaImpactadaDidntApp == false) {
                $form = $this->createForm(MudancasGestorSoftwareAppType::class, $mudSoft);
            } else {
                $form = $this->createForm(MudancasGestorSoftwareType::class, $mudSoft);
            }
            $form->handleRequest($request);


            if ($form->isSubmitted()) {
                $data = $request->request;
                if ($areaImpactadaDidntApp == false) {
                    $em->flush();
                    $mud->setAppGest($mudSoft->getAppGestor());
                    $mud->setComGest($mudSoft->getCommentGestor());
                    date_default_timezone_set("America/Sao_Paulo");
                    $time = new \DateTime();
                    $mud->setDateAG($time);
                    $em->flush();
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

                    $em->persist($mud);
                    $em->flush();
                }

                for ($i = 1; $i <= ((sizeof($data) - 1) / 3); $i++) {
                    $steps = new StepsGestor();
                    $steps->setMudancasSoftware($mudSoft);
                    $steps->setStep($data->get($i));
                    $steps->setComment($data->get(strval($i) . 'desc'));
                    $steps->setDate($data->get(strval($i) . 'date'));
                    $em->persist($steps);
                    $em->flush();
                    if ($request->files->get(strval($i) . 'file') != null) {
                        $fileName = $steps->getId() . '_' . $mudSoft->getId() . '.' . $request->files->get(strval($i) . 'file')->guessExtension();
                        $publicDirectory = $this->getParameter('kernel.project_dir');
                        $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
                        $request->files->get(strval($i) . 'file')->move($excelFilepath, $fileName);
                        $steps->setDoc($fileName);
                    }
                    $mudSoft->addStepsGestor($steps);
                    $em->flush();
                }
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }
            return $this->render('mudancas_software/gestor_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'form' => $form->createView(),
                'person' => $person,
                'manager' => $manager,
                'formImp' => $formImp->createView(),
                'formTesters' => $formTesters->createView(),
                'formTestersTi' => $formTestersTi->createView(),
                'formDevs' => $formDevs->createView(),
                'areaImpactadaDidntApp' => $areaImpactadaDidntApp,
                'm' => $mud,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/delete/stepGestor/{id}/{idS}/{idm}', name: 'deleteStepGestor')]
    public function deleteStepGestor(ManagerRegistry $doctrine, Request $request, $id, $idS, $idm)
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
            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($idS);
            $stepGestor = $em->getRepository(StepsGestor::class)->find($id);
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
            if ($stepGestor->getDoc() != null) {
                $delete  = unlink($excelFilepath . '/' . $stepGestor->getDoc());
                if ($delete) {
                    echo "delete success";
                } else {
                    echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepsGestor($stepGestor);
            $em->remove($stepGestor);
            $em->flush();
            return $this->redirectToRoute('gestor_software', ['id' => $idm]);
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

            $mudSoft = $mud->getMudS();

            $form = $this->createForm(MudancasSoftwareSolicitanteType::class, $mudSoft);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            }

            return $this->render('mudancas_software/Solicitante_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'm' => $mud,
                'form' => $form->createView(),
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

            $mudSoft = $mud->getMudS();

            //
            $perm = false;
            foreach ($mudSoft->getDevelopers() as $key => $value) {
                if ($value == $person) {
                    $perm = true;
                }
            }

            $form = $this->createForm(MudancasSoftwareDevType::class, $mudSoft);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            }

            return $this->render('mudancas_software/dev_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'm' => $mud,
                'perm' => $perm,
                'form' => $form->createView(),
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/delete/stepDev/{id}/{idS}/{idm}', name: 'deleteStepDev')]
    public function deleteStepDev(ManagerRegistry $doctrine, Request $request, $id, $idS, $idm)
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
            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($idS);
            $stepDev = $em->getRepository(StepsDev::class)->find($id);
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId() . '/dev';
            if ($stepDev->getDoc() != null) {
                $delete  = unlink($excelFilepath . '/' . $stepDev->getDoc());
                if ($delete) {
                    echo "delete success";
                } else {
                    echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepDev($stepDev);
            $em->remove($stepDev);
            $em->flush();
            return $this->redirectToRoute('dev_software', ['id' => $idm]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas/software/Dev/add/{id}/{idm}', name: 'AddDev_software')]
    public function AddDev(ManagerRegistry $doctrine, Request $request, $id, $idm)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();

            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($id);

            $data = $request->request;
            for ($i = 1; $i <= (sizeof($data) / 4); $i++) {
                $steps = new StepsDev();
                $steps->setMudancasSoftware($mudSoft);
                $steps->setStep($data->get($i));
                $steps->setComment($data->get(strval($i) . 'desc'));
                $steps->setDate($data->get(strval($i) . 'date'));
                $steps->setDateEnd($data->get(strval($i) . 'date2'));
                $steps->setAffect($data->get(strval($i) . 'inputtest'));

                $em->persist($steps);
                $em->flush();
                if ($request->files->get(strval($i) . 'files') != null) {
                    $fileName = $steps->getId() . '_' . $mudSoft->getId() . '.' . $request->files->get(strval($i) . 'files')->guessExtension();
                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId() . '/dev';
                    $request->files->get(strval($i) . 'files')->move($excelFilepath, $fileName);
                    $steps->setDoc($fileName);
                }
                $mudSoft->addStepDev($steps);
                $em->flush();
            }
            return $this->redirectToRoute('dev_software', ['id' => $idm]);
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

            $mudSoft = $mud->getMudS();

            foreach ($mud->getMudS()->getStepDev() as $key => $value) {
                if ($value->getAffect() == 'Sim') {
                }
            }


            $form = $this->createForm(MudancasSoftwareTestesTiType::class, $mudSoft);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            }
            $perm = false;
            foreach ($mudSoft->getTestersti() as $key => $value) {
                if ($value == $person) {
                    $perm = true;
                }
            }



            return $this->render('mudancas_software/test_ti_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'm' => $mud,
                'perm' => $perm,
                'form' => $form->createView(),
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas/software/Test/IT/add/{id}/{idm}', name: 'AddTestTi_software')]
    public function AddTestTi(ManagerRegistry $doctrine, Request $request, $id, $idm)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();

            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($id);

            $data = $request->request;
            for ($i = 1; $i <= (sizeof($data) / 4); $i++) {
                $steps = new StepsTest();
                $steps->setMudancasSoftware($mudSoft);
                $steps->setStep($data->get($i));
                $steps->setComment($data->get(strval($i) . 'desc'));
                $steps->setDate($data->get(strval($i) . 'date'));
                $steps->setAprove($data->get(strval($i) . 'test'));

                $em->persist($steps);
                $em->flush();
                if ($request->files->get(strval($i) . 'file') != null) {
                    $fileName = $steps->getId() . '_' . $mudSoft->getId() . '.' . $request->files->get(strval($i) . 'file')->guessExtension();
                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId() . '/test/ti/';
                    $request->files->get(strval($i) . 'file')->move($excelFilepath, $fileName);
                    $steps->setDoc($fileName);
                }
                $mudSoft->addStepsTest($steps);
                $em->flush();
            }
            return $this->redirectToRoute('test_ti_software', ['id' => $idm]);
        }
    }

    #[Route('/delete/TestTi/{id}/{idS}/{idm}', name: 'deleteTestTi')]
    public function deleteTestTi(ManagerRegistry $doctrine, Request $request, $id, $idS, $idm)
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
            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($idS);
            $StepsTest = $em->getRepository(StepsTest::class)->find($id);
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId() . '/test/ti';
            if ($StepsTest->getDoc() != null) {
                $delete  = unlink($excelFilepath . '/' . $StepsTest->getDoc());
                if ($delete) {
                    echo "delete success";
                } else {
                    echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepsTest($StepsTest);
            $em->remove($StepsTest);
            $em->flush();
            return $this->redirectToRoute('test_ti_software', ['id' => $idm]);
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

            $mudSoft = $mud->getMudS();
            $perm = false;
            foreach ($mudSoft->getTesters() as $key => $value) {
                if ($value == $person) {
                    $perm = true;
                }
            }
            return $this->render('mudancas_software/test_solicitante_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'm' => $mud,
                'perm' => $perm,
                'creation' => 'false',
                'person' => $person,
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/mudancas/software/Test/Sol/add/{id}/{idm}', name: 'AddTestSol_software')]
    public function AddTestSol(ManagerRegistry $doctrine, Request $request, $id, $idm)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($id);
            $data = $request->request;
            for ($i = 1; $i <= (sizeof($data) / 4); $i++) {
                $steps = new StepsTestSol();
                $steps->setMudancasSoftware($mudSoft);
                $steps->setStep($data->get($i));
                $steps->setComment($data->get(strval($i) . 'desc'));
                $steps->setDate($data->get(strval($i) . 'date'));
                $steps->setAprove($data->get(strval($i) . 'test'));
                $em->persist($steps);
                $em->flush();
                if ($request->files->get(strval($i) . 'file') != null) {
                    $fileName = $steps->getId() . '_' . $mudSoft->getId() . '.' . $request->files->get(strval($i) . 'file')->guessExtension();
                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId() . '/test/sol/';
                    $request->files->get(strval($i) . 'file')->move($excelFilepath, $fileName);
                    $steps->setDoc($fileName);
                }
                $mudSoft->addStepsTestSol($steps);
                $em->flush();
            }
            return $this->redirectToRoute('test_solicitante_software', ['id' => $idm]);
        }
    }

    #[Route('/delete/TestSol/{id}/{idS}/{idm}', name: 'deleteTestSol')]
    public function deleteTestSol(ManagerRegistry $doctrine, Request $request, $id, $idS, $idm)
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
            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($idS);
            $StepsTest = $em->getRepository(StepsTestSol::class)->find($id);
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId() . '/test/sol';
            if ($StepsTest->getDoc() != null) {
                $delete  = unlink($excelFilepath . '/' . $StepsTest->getDoc());
                if ($delete) {
                    echo "delete success";
                } else {
                    echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepsTestSol($StepsTest);
            $em->remove($StepsTest);
            $em->flush();
            return $this->redirectToRoute('test_solicitante_software', ['id' => $idm]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    #[Route('/close/TestSol/{idS}/{idm}', name: 'CloseTestSol')]
    public function CloseTestSol(ManagerRegistry $doctrine, Request $request,  $idS, $idm)
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
            $mud = $em->getRepository(Mudancas::class)->find($idm);
            $mudSoft = $em->getRepository(MudancasSoftware::class)->find($idS);

            $mudSoft->setDocTestSol('Done');
            $mud->setDone('Feito');

            $em->flush();
            return $this->redirectToRoute('test_solicitante_software', ['id' => $idm]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }

    /**
     * Sends an email using PHPMailer.
     *
     * @param ManagerRegistry $doctrine The Doctrine ManagerRegistry instance.
     * @param mixed $request The request object.
     * @param mixed $sendTo The email address to send the email to.
     * @param mixed $mud The MudancasSoftware object.
     * @param mixed $per The Per object.
     * @param mixed $demand The demand object.
     * @param mixed $gestor The gestor object.
     * @param mixed $client The client object (optional).
     * @return PHPMailer The PHPMailer instance used to send the email.
     */
    public function sendEmail(ManagerRegistry $doctrine, $sendTo, $mud, $per, $demand,  $gestor, $client = null)
    {
        $em = $doctrine->getManager();
        /**
         * This code retrieves the configuration email from the database and creates a new configuration if it doesn't exist.
         * The configuration includes the SMTP server details, email credentials, and other settings.
         * 
         * @param EntityManagerInterface $em The entity manager used to interact with the database.
         * @return void
         */
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
                try {
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
                } catch (\Throwable $th) {
                    //throw $th;
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
        } catch (Exception $ex) {
            //   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        return;
    }
}
