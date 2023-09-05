<?php

namespace App\Controller;

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
use App\Form\MudancasGestorSoftwareType;
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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            
            if (($mud->getAddBy() == $person & $mudSoft->getRef() != null) || $mud->getAddBy() != $person) {
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
                    'person' => $person,
                ]);
            } else {
                return $this->redirectToRoute('Softindex', ['id' => $mud->getId()]);
            }
        } else {
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
            $mudSoft = $mud->getMudS();
            $form = $this->createForm(MudancasSoftwareGerenteDeAprovacaoType::class, $mudSoft);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if($request->request->get('1test') == 'Sim'){
                    $mud->setAppMan(1);
                    if($request->request->get('2test') != null){
                        $mud->setComMan($request->request->get('2test'));
                    }
                    date_default_timezone_set("America/Sao_Paulo");
                    $time = new \DateTime();
                    $mud->setDateAM($time);
                    $em->flush();
                }
                $mud->setMangerMudancas($mudSoft->getGestor());
                $em->flush();
                return $this->redirectToRoute('gestor_software', ['id' => $mud->getId()]);
            }
            return $this->render('mudancas_software/gerente_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'form' => $form->createView(),
                'person' => $person,
                'm' => $mud,
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

            $formTesters = $this->createForm(MudancasSoftwareTestersType::class, $mudSoft);
            $formTesters->handleRequest($request);
            
            if ($formTesters->isSubmitted() && $formTesters->isValid()) { 
                $em->flush();
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }

            $formTestersTi = $this->createForm(MudancasSoftwareTestersTiType::class, $mudSoft);
            $formTestersTi->handleRequest($request);
            
            if ($formTestersTi->isSubmitted() && $formTestersTi->isValid()) { 
                $em->flush();
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }

            $formDevs = $this->createForm(MudancasSoftwareDevsType::class, $mudSoft);
            $formDevs->handleRequest($request);
            
            if ($formDevs->isSubmitted() && $formDevs->isValid()) { 
                $em->flush();
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }

            $form = $this->createForm(MudancasGestorSoftwareType::class, $mudSoft);
            $form->handleRequest($request);

            if ($form->isSubmitted() ) {
                $data = $request->request;
                $em->flush();
                if((sizeof($data) > 1)){
                    if($areaImpactadaDidntApp == false){ 
                        $mud->setAppGest($data->get('app'));
                        $mud->setComGest($data->get('appCom'));
                        date_default_timezone_set("America/Sao_Paulo");
                        $time = new \DateTime();
                        $mud->setDateAG($time);
                        $em->flush();
                        for ($i=1; $i <= ((sizeof($data)-3)/3) ; $i++) {
                            $steps = new StepsGestor();
                            $steps->setMudancasSoftware($mudSoft);
                            $steps->setStep($data->get($i));
                            $steps->setComment($data->get(strval($i).'desc'));
                            $steps->setDate($data->get(strval($i).'date'));
                            $em->persist($steps);
                            $em->flush();
                            if($request->files->get(strval($i).'file') != null){
                                $fileName = $steps->getId().'_'.$mudSoft->getId().'.'. $request->files->get(strval($i).'file')->guessExtension();
                                $publicDirectory = $this->getParameter('kernel.project_dir');
                                $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
                                $request->files->get(strval($i).'file')->move($excelFilepath, $fileName);
                                $steps->setDoc($fileName);
                            }
                            $mudSoft->addStepsGestor($steps);
                            $em->flush();
                        }
                    }else{
                        for ($i=1; $i <= ((sizeof($data)-1)/3) ; $i++) {
                            $steps = new StepsGestor();
                            $steps->setMudancasSoftware($mudSoft);
                            $steps->setStep($data->get($i));
                            $steps->setComment($data->get(strval($i).'desc'));
                            $steps->setDate($data->get(strval($i).'date'));
                            $em->persist($steps);
                            $em->flush();
                            if($request->files->get(strval($i).'file') != null){
                                $fileName = $steps->getId().'_'.$mudSoft->getId().'.'. $request->files->get(strval($i).'file')->guessExtension();
                                $publicDirectory = $this->getParameter('kernel.project_dir');
                                $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
                                $request->files->get(strval($i).'file')->move($excelFilepath, $fileName);
                                $steps->setDoc($fileName);
                            }
                            $mudSoft->addStepsGestor($steps);
                            $em->flush();
                        }
                    }
                    return $this->redirectToRoute('gestor_software', ['id' => $id]);
                }
                return $this->redirectToRoute('gestor_software', ['id' => $id]);
            }
            return $this->render('mudancas_software/gestor_software.html.twig', [
                'controller_name' => 'MudancasSoftwareController',
                'login' => 'null',
                'creation' => 'false',
                'form' => $form->createView(),
                'person' => $person,
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

    #[Route('/delete/stepGestor/{id}/{idS}/{idm}', name:'deleteStepGestor')]
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
            if ( $stepGestor->getDoc() != null) {
                $delete  = unlink($excelFilepath.'/'. $stepGestor->getDoc());
                if($delete){
	                echo "delete success";
                }else{
	                echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepsGestor($stepGestor);
            $em->remove($stepGestor);
            $em->flush();
            return $this->redirectToRoute('gestor_software', ['id' => $idm]);
        }else {
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
                if($value == $person){
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

    #[Route('/delete/stepDev/{id}/{idS}/{idm}', name:'deleteStepDev')]
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
            $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId().'/dev';
            if ( $stepDev->getDoc() != null) {
                $delete  = unlink($excelFilepath.'/'. $stepDev->getDoc());
                if($delete){
	                echo "delete success";
                }else{
	                echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepDev($stepDev);
            $em->remove($stepDev);
            $em->flush();
            return $this->redirectToRoute('dev_software', ['id' => $idm]);
        }else {
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
            for ($i=1; $i <= (sizeof($data)/4) ; $i++) {
                $steps = new StepsDev();
                $steps->setMudancasSoftware($mudSoft);
                $steps->setStep($data->get($i));
                $steps->setComment($data->get(strval($i).'desc'));
                $steps->setDate($data->get(strval($i).'date'));
                $steps->setDateEnd($data->get(strval($i).'date2'));

                $em->persist($steps);
                $em->flush();
                if($request->files->get(strval($i).'files') != null){
                    $fileName = $steps->getId().'_'.$mudSoft->getId().'.'. $request->files->get(strval($i).'files')->guessExtension();
                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId(). '/dev';
                    $request->files->get(strval($i).'files')->move($excelFilepath, $fileName);
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

            $form = $this->createForm(MudancasSoftwareTestesTiType::class, $mudSoft);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            }
            $perm = false;
            foreach ($mudSoft->getTestersti() as $key => $value) {
                if($value == $person){
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
            for ($i=1; $i <= (sizeof($data)/4) ; $i++) {
                $steps = new StepsTest();
                $steps->setMudancasSoftware($mudSoft);
                $steps->setStep($data->get($i));
                $steps->setComment($data->get(strval($i).'desc'));
                $steps->setDate($data->get(strval($i).'date'));
                $steps->setAprove($data->get(strval($i).'test'));

                $em->persist($steps);
                $em->flush();
                if($request->files->get(strval($i).'file') != null){
                    $fileName = $steps->getId().'_'.$mudSoft->getId().'.'. $request->files->get(strval($i).'file')->guessExtension();
                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId(). '/test/ti/';
                    $request->files->get(strval($i).'file')->move($excelFilepath, $fileName);
                    $steps->setDoc($fileName);
                }
                $mudSoft->addStepsTest($steps);
                $em->flush();
            }
            return $this->redirectToRoute('test_ti_software', ['id' => $idm]);
        }
    }

    
    #[Route('/delete/TestTi/{id}/{idS}/{idm}', name:'deleteTestTi')]
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
            $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId().'/test/ti';
            if ( $StepsTest->getDoc() != null) {
                $delete  = unlink($excelFilepath.'/'. $StepsTest->getDoc());
                if($delete){
	                echo "delete success";
                }else{
	                echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepsTest($StepsTest);
            $em->remove($StepsTest);
            $em->flush();
            return $this->redirectToRoute('test_ti_software', ['id' => $idm]);
        }else {
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
            foreach ($mudSoft->getTestersti() as $key => $value) {
                if($value == $person){
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
            for ($i=1; $i <= (sizeof($data)/4) ; $i++) {
                $steps = new StepsTestSol();
                $steps->setMudancasSoftware($mudSoft);
                $steps->setStep($data->get($i));
                $steps->setComment($data->get(strval($i).'desc'));
                $steps->setDate($data->get(strval($i).'date'));
                $steps->setAprove($data->get(strval($i).'test'));
                $em->persist($steps);
                $em->flush();
                if($request->files->get(strval($i).'file') != null){
                    $fileName = $steps->getId().'_'.$mudSoft->getId().'.'. $request->files->get(strval($i).'file')->guessExtension();
                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId(). '/test/sol/';
                    $request->files->get(strval($i).'file')->move($excelFilepath, $fileName);
                    $steps->setDoc($fileName);
                }
                $mudSoft->addStepsTestSol($steps);
                $em->flush();
            }
            return $this->redirectToRoute('test_solicitante_software', ['id' => $idm]);
        }
    }


    #[Route('/delete/TestSol/{id}/{idS}/{idm}', name:'deleteTestSol')]
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
            $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId().'/test/sol';
            if ( $StepsTest->getDoc() != null) {
                $delete  = unlink($excelFilepath.'/'. $StepsTest->getDoc());
                if($delete){
	                echo "delete success";
                }else{
	                echo "delete not success";
                }
            }
            //delete Step Gestor
            $mudSoft->removeStepsTestSol($StepsTest);
            $em->remove($StepsTest);
            $em->flush();
            return $this->redirectToRoute('test_solicitante_software', ['id' => $idm]);
        }else {
                return $this->redirectToRoute('log_employer');
        }
    }

}
