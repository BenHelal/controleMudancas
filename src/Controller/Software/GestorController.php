<?php

namespace App\Controller\Software;

use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Steps;
use App\Entity\StepsGestor;
use App\Form\GestorSoftware\iniciarType;
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
            return $this->render('software/gestor/documentation.html.twig', [
                'login' => 'null',
                'person' => $person,
                'controller_name' => 'GestorController',
            ]);
        } else {
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
            $formInit = $this->createForm(iniciarType::class, $muds);
            $formInit->handleRequest($request);
            if (
                $formInit->isSubmitted() &&
                $formInit->isValid()
            ) {
                $em->persist($mud);
                $em->flush();
            }

            //steps Gestor 
            $SD =  $muds->getStepsGestor();
            return $this->render('software/gestor/documentation.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'formInit' => $formInit,
                'controller_name' => 'GestorController',
                'sd' => $SD,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * Renders the documentation page for the GestorController.
     *
     * @Route("/software/gestor/add/documentation/{id}", name="app_software_gestor_add_documentation")
     * @return Response
     */
    public function addDocumentation(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = $request->getSession();
        
        if ($session->get('token_jwt') === '') {
            return $this->redirectToRoute('app_login');
        }
    
        $em = $doctrine->getManager();
        $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
        $mud = $em->getRepository(Mudancas::class)->find($id);
        
        if (!$mud) {
            // Handle case when the Mudancas entity with the given $id is not found.
            // You might want to return an appropriate response or redirect.
            return new Response('Mudancas not found', 404);
        }
    
        $muds = $mud->getMudS();
        $data = $request->request;
    
       
        for ($i = 1; $i <= sizeof($data)/3 ; $i++) {

            $steps = new StepsGestor();
            $steps->setMudancasSoftware($muds);$steps->setStep((string) $data->get($i));
            $steps->setComment($data->get(strval($i) . 'desc'));
            $steps->setDate($data->get(strval($i) . 'date'));
            $em->persist($steps);
            $em->flush();

            // Repeated logic for handling both 'file' and 'files'
            $fileKey = strval($i) . 'file';
            if ($request->files->get($fileKey) !== null) {
                $this->handleFileUpload($request, $steps, $mud, $fileKey);
            }
    
            $filesKey = strval($i) . 'files';
            if ($request->files->get($filesKey) !== null) {
                $this->handleFileUpload($request, $steps, $mud, $filesKey);
            }
    
            $muds->addStepsGestor($steps);
        }
        $em->flush();
        // Add any additional logic or response if needed after the loop
    
        return $this->redirectToRoute('app_software_gestor_documentation', ['id' => $id]);
    }

    private function handleFileUpload(Request $request, $steps, Mudancas $mud, $fileKey)
    {

        $fileName = $steps->getId() .''. $fileKey .'_'. $mud->getId() . '.' . $request->files->get($fileKey)->guessExtension();
        $publicDirectory = $this->getParameter('kernel.project_dir');
        $excelFilepath = $publicDirectory . '/public/assets/' . $mud->getId();
        $request->files->get($fileKey)->move($excelFilepath, $fileName);
        if($fileKey == '1file'){
            $steps->setDoc($fileName);    
        }else{
            $steps->setDocTest($fileName);  
        }
    }


    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/gestor/test/{id}", name="app_software_gestor_test")
     * @return Response
     */
    public function test(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $mudSoft = $mud->getMudS();
            //iniciarType
            $formInit = $this->createForm(iniciarType::class, $mud);
            $formInit->handleRequest($request);
            if (
                $formInit->isSubmitted() &&
                $formInit->isValid()
            ) {
                $em->persist($mud);
                $em->flush();
            }



            return $this->render('software/gestor/test.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'mudS' => $mudSoft,
                'formInit' => $formInit,
                'controller_name' => 'GestorController',
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }


    function compareSteps($step1, $step2) {
        return $step1->getId() == $step2->getId();
    }
    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/gestor/steps/{id}", name="app_software_gestor_steps")
     * @return Response
     */
    public function steps(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $muds = $mud->getMudS();

            //steps Gestor 
            $sd = [];
            $s = [];
            $SD =  $muds->getStepsGestor();

            foreach ($SD as $key => $value) {
                # code...
                if($value->getApproveSol() =='Aprovar'){
                    array_push($sd, $value);
                }

            }

            foreach ($SD as $keys=> $val) {
                
                foreach ($val->getSteps() as $keys=> $values) {
                    # code...
                    array_push($s, $values);
                }
            }
            
            return $this->render('software/gestor/steps.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $sd,
                'step' => $s,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }


    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/gestor/add/steps/{id}", name="app_software_gestor_add_steps")
     * @return Response
     */
    public function Addsteps(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        
        
        
        if ($session->get('token_jwt') === '') {
            return $this->redirectToRoute('app_login');
        }
    
        $em = $doctrine->getManager();
        $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
        $mud = $em->getRepository(Mudancas::class)->find($id);
        
        if (!$mud) {
            // Handle case when the Mudancas entity with the given $id is not found.
            // You might want to return an appropriate response or redirect.
            return new Response('Mudancas not found', 404);
        }
    
        $muds = $mud->getMudS();
        $data = $request->request;
        $em = $doctrine->getManager();
        $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
        $mud = $em->getRepository(Mudancas::class)->find($id);
        $muds = $mud->getMudS();

        for ($i = 1; $i <= sizeof($data)/4 ; $i++) {
            $stepsGestor = $em->getRepository(StepsGestor::class)->find($data->get($i.'a'));
            $steps = new Steps();
            $steps->setAriquivo($stepsGestor);
            $steps->setComments($data->get(strval($i) . 'desc'));
            $steps->setDateCreation($data->get(strval($i) . 'date'));
            $steps->setTitle($data->get(strval($i)));
            $em->persist($steps);
            $em->flush();
            // Repeated logic for handling both 'file' and 'files'
            $fileKey = strval($i) . 'file';
            if ($request->files->get($fileKey) !== null) {
                $this->handleFileUpload($request, $steps, $mud, $fileKey);
            }
            $filesKey = strval($i) . 'files';
            if ($request->files->get($filesKey) !== null) {
                $this->handleFileUpload($request, $steps, $mud, $filesKey);
            }
        }
        
        $em->flush();
        // Add any additional logic or response if needed after the loop
        return $this->redirectToRoute('app_software_gestor_steps', ['id' => $id]);
    }
}
