<?php

namespace App\Controller\Software;

use App\Entity\ApiToken;
use App\Entity\ConfigEmail;
use App\Entity\Email;
use App\Entity\EmailToSendConfig;
use App\Entity\IA;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Process;
use App\Entity\SectorProcess;
use App\Entity\Steps;
use App\Entity\StepsGestor;
use App\Form\GestorSoftware\iniciarType;
use App\Form\MudancasgestorImpType;
use App\Form\MudancasGestorSoftwareAppType;
use App\Form\MudancasSoftwareDevsType;
use App\Model\Class\IpAdress;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use GeminiAPI\Client as GeminiAPIClient;
use GeminiAPI\Resources\Parts\TextPart;
use ZipArchive;

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
            $filesAssociative = [];
            foreach ($SD as $key => $sd) {
                # code...
                if ($SD != null) {
                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath2 = $publicDirectory . '/public/assets/' . $mud->getId() . '/documentation/' . $sd->getId();
                    try {
                        //code...
                        $files = scandir($excelFilepath2);
                        $files = array_diff($files, ['.', '..']);

                        // Create an associative array with key as $sd->getId() and value as $files
                        $filesAssociative[$sd->getId()] = $files;
                    } catch (\Throwable $th) {
                        // Handle the exception, if needed
                        $filesAssociative[$sd->getId()] = [];
                    }
                } else {
                    $filesAssociative[$sd->getId()] = [];
                }
            }

            $isTheLastApprove = true;
            if ($mud->getMudS() == null) {
                $isTheLastApprove = false;
            }

            $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mud]);
            $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
            foreach ($sps as $key => $sp) {
                if ($sp->getAppSectorMan() != 1) {
                    $isTheLastApprove = false;
                }
            }
            if($mud->getClient() != null){
                $token = $em->getRepository(ApiToken::class)->findOneBy(['mud' => $mud]);

                if($token != null){
                $url = "10.100.2.61/ClientExteranlAcces/public/get/data";
                //The data you want to send via POST
                $fields = [
                    'token'=> $token->getToken(),
                    'id'=> $mud->getId(),
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

                $appClt = $cl['mud']['TokenData']['appClt'];
                
                if ($isTheLastApprove) {
                        
                    if($appClt == "2"){
                        $isTheLastApprove = false;
                        
                        $mud->setImplemented(2);
                        $mud->setDone('Feito');
                        $em->flush();
                        return $this->redirectToRoute('flow', ['id' => $mud->getId()]);
                    }elseif($appClt == "1"){
                        $isTheLastApprove = true;
                    }else{
                        $isTheLastApprove = false;
                    }
                }    
            }

            $ia = $em->getRepository(IA::class)->find(1);

            return $this->render('software/gestor/documentation.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'formInit' => $formInit,
                'controller_name' => 'GestorController',
                'sd' => $SD,
                'files' => $filesAssociative,
                'iTLA' => $isTheLastApprove,
                'iaPerm' => $ia->HeIsPerm($person)
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


        for ($i = 1; $i <= sizeof($data) / 3; $i++) {

            $steps = new StepsGestor();
            $steps->setMudancasSoftware($muds);
            $steps->setStep((string) $data->get($i));
            $steps->setComment($data->get(strval($i) . 'desc'));
            $steps->setDate($data->get(strval($i) . 'date'));
            $em->persist($steps);
            $em->flush();

            // Repeated logic for handling both 'file' and 'files'
            $fileKey = strval($i) . 'file';
            //
            /*foreach ($_FILES[strval($fileKey)]['name']) as $key => $value) {
                # code...
            }*/
            if ($request->files->get($fileKey) !== null) {
                foreach ($request->files->get($fileKey) as $key => $value) {

                    date_default_timezone_set("America/Sao_Paulo");

                    $time = new \DateTime();
                    $formattedTime = $time->format('Y-m-d_H-i-s'); // Use underscore instead of colon for filename
                    $fileName = $formattedTime . '_' . $value->getClientOriginalName();

                    $publicDirectory = $this->getParameter('kernel.project_dir');
                    $excelFilepath = $publicDirectory . '/public/assets/' . $mud->getId() . '/documentation/' . $steps->getId();
                    $value->move($excelFilepath, $fileName);
                    $steps->setDoc('documentation');
                }
            }

            $filesKey = strval($i) . 'files';
            if ($request->files->get($filesKey) !== null) {
                $this->handleFileUpload($request, $steps, $mud, $filesKey);
            }

            $muds->addStepsGestor($steps);
            $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '3']);
            $email = new  Email();
            $email->setMudancas($mud);
            $email->setSendTo($mud->getAddBy());
            $email->setSendBy($person);
            $email->setTitle($emailConfigSoftware->getSubjectMessage());
            $email->setBody($emailConfigSoftware->getTitleOfMessage());
            $em->persist($email);
            $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $data->get(strval($i) . 'desc'));
        }

        /*    $email = new  Email();
        $email->setMudancas($mud);
        $email->setSendTo($mud->getAddBy());
        $email->setSendBy($person);
        $email->setTitle('Gestor adiciona Arquivos ');
        $email->setBody('AddArquivos');
        $em->persist($email);
        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
        */

        $em->flush();
        // Add any additional logic or response if needed after the loop

        return $this->redirectToRoute('app_software_gestor_documentation', ['id' => $id]);
    }

    /**
     * generate documentations AI from description project
     * @Route("/software/gestor/IA/documentation/{id}", name="app_software_gestor_IA_documentation")
     * @return Response
     */
    public function IADocumentation(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = $request->getSession();
        if ($session->get('token_jwt') === '') {
            return $this->redirectToRoute('app_login');
        }
        $em = $doctrine->getManager();
        $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
        $ia = $em->getRepository(IA::class)->find(1);
        if ($ia->HeIsPerm($person)) {
            $mud = $em->getRepository(Mudancas::class)->find($id);
            if (!$mud) {
                // Handle case when the Mudancas entity with the given $id is not found.
                // You might want to return an appropriate response or redirect.
                return new Response('Mudancas not found', 404);
            }
            $data = $request->request; 
            $input = $data->get(strval($id) . 'desc');
            $words = str_word_count($input);

            if ($words > 10) {

                try {
                    //code...
                $client = new GeminiAPIClient('AIzaSyCsf41aD_WbpzTYYSIzLN4aBOcrspiCQpM');
                $response = $client->geminiPro()->generateContent(
                    new TextPart($input . '., develope the idea and generate description more details'.$data->get('c1').' '.$data->get('c2').'   '.$data->get('c3').'  '.$data->get('c4').'  , in  portuguese , '.$data->get('c5')),
                );

                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                // Add a section to the document
                $section = $phpWord->addSection();
                // Add a title

                // Add an image
                $imagePath = 'serdia_logo.png';
                $section->addImage($imagePath, array('width' => 200, 'height' => 40, 'marginTop' => 10, 'marginLeft' => 10));
                $section->addText('Documentação gerada por IA', array('size' => 16, 'bold' => true));
                $section->addTextBreak();
                // Add text to the section
                $array = explode("\n", $response->text());
                foreach ($array as $value) {
                    // Regular expression to find text between **
                    $pattern = '/\*\*(.*?)\*\*/';
                    preg_match_all($pattern, $value, $matches);

                    // Replace ** with empty string and apply bold formatting
                    foreach ($matches[0] as $match) {
                        $replace = str_replace('**', '', $match);
                        $value = str_replace($match, $replace, $value);
                    }

                    // Add the modified text to the document
                    $section->addText($value);
                    // Add a new line
                    $section->addTextBreak();
                }

                $filename = 'IADoc'.$id.'.docx';
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($filename);

                $publicDirectory = $this->getParameter('kernel.project_dir');
                $excelFilepath = $publicDirectory . '/public/' . $filename;

                $response = new BinaryFileResponse($excelFilepath);
                $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $filename
                );

                return $response;
            } catch (\Throwable $th) {
                //throw $th;
                
                return $this->redirectToRoute('app_software_gestor_documentation', ['id' => $id]);
            }
            } else {
                echo "A entrada deve conter mais de 10 palavras.";
                return $this->redirectToRoute('app_software_gestor_documentation', ['id' => $id]);
            }
        } else {
            return $this->redirectToRoute('app_software_gestor_documentation', ['id' => $id]);
        }
    }

    /**
     * @Route("/download-zip/{id}/{sId}", name="download_zip")
     */
    public function downloadZipAction($id, $sId)
    {
        $publicDirectory = $this->getParameter('kernel.project_dir');
        $excelFilepath = $publicDirectory . '/public/assets/' . $id . '/documentation/' . $sId;
        $zipFilename = $publicDirectory . '/public/assets/' . $id . '/documentation/Docs_' . $sId . '.zip';


        $this->zipFolder($excelFilepath, $zipFilename);

        $response = new BinaryFileResponse($zipFilename);
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'folder.zip'
        );

        return $response;
    }


    private function zipFolder(string $sourceFolder, string $zipFilename)
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(dirname($zipFilename));

        $zip = new ZipArchive();
        $zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceFolder),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $filePath = $item->getRealPath();
            $relativePath = substr($filePath, strlen($sourceFolder) + 1);

            if ($item->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }


    private function handleFileUpload(Request $request, $steps, Mudancas $mud, $fileKey)
    {

        $fileName = $steps->getId() . '' . $fileKey . '_' . $mud->getId() . '.' . $request->files->get($fileKey)->guessExtension();
        $publicDirectory = $this->getParameter('kernel.project_dir');
        $excelFilepath = $publicDirectory . '/public/assets/' . $mud->getId();
        $request->files->get($fileKey)->move($excelFilepath, $fileName);
        if ($fileKey == '1file') {
            $steps->setDoc($fileName);
        } else {
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
            $muds = $mud->getMudS();

            //steps Gestor 
            $sd = [];
            $s = [];
            $SD =  $muds->getStepsGestor();

            foreach ($SD as $key => $value) {
                # code...
                if ($value->getApproveSol() == 'Approvar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "teste ti") {
                        array_push($s, $values);
                    }
                }
            }

            // To implementad
            $sImp = [];
            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "aguardando implementação") {
                        array_push($sImp, $values);
                    }
                }
            }

            return $this->render('software/gestor/test.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $sd,
                'step' => $s,
                'sImp' => $sImp
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/gestor/test/approve/{id}", name="app_software_gestor_test_approve")
     * @return Response
     */
    public function testApprove(ManagerRegistry $doctrine, Request $request, $id): Response
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
                if ($value->getApproveSol() == 'Approvar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "teste ti" || $values->getStatus() == "aguardando implementação") {
                        array_push($s, $values);
                    }
                }
            }

            $data = $request->request;
                
                foreach ($s as $key => $value) {
                    
                    $task = $em->getRepository(Steps::class)->find($value->getId());
                    if ($request->files->get(strval($value->getId()) . 'files') != null) {
                        $fileName = $value->getId() . '_TEST_Gestor_' . $muds->getId() . '.' . $request->files->get(strval($value->getId()) . 'files')->guessExtension();
                        $publicDirectory = $this->getParameter('kernel.project_dir');
                        $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
                        $request->files->get(strval($value->getId()) . 'files')->move($excelFilepath, $fileName);
                        $value->setDocGestor($fileName);
                    }
                    if ($data->get($value->getId() . 'stat') == 'Approvar') {
                        $value->setStatus("teste usuário");
                        $em->flush();
                        # code...

                        $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '9']);
                        $email = new  Email();
                        $email->setMudancas($mud);
                        $email->setSendTo($mud->getAddBy());
                        $email->setSendBy($person);
                        $email->setTitle($emailConfigSoftware->getSubjectMessage());
                        $email->setBody($emailConfigSoftware->getTitleOfMessage());
                        $em->persist($email);
                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $data->get($value->getId() . 'de'),fase:$task);
                    } elseif ($data->get($value->getId() . 'stat') == 'Reprovar') {
                        $value->setStatus("pendência");
                        $em->flush();

                        if (sizeof($muds->getDevelopers()) >= 0) {
                            foreach ($muds->getDevelopers() as $key => $valuess) {
                                # code...

                                $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '8']);
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($valuess);
                                $email->setSendBy($person);
                                $email->setTitle($emailConfigSoftware->getSubjectMessage());
                                $email->setBody($emailConfigSoftware->getTitleOfMessage());
                                $em->persist($email);
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $data->get($value->getId() . 'de'),fase:$task);
                            }
                        }
                    } elseif ($data->get($value->getId() . 'stat') == 'não implementado') {
                        $value->setStatus("não implementado");
                        $em->flush();
                    } elseif ($data->get($value->getId() . 'stat') == 'implementado') {
                        $value->setStatus("implementado");
                        $em->flush();
                    } elseif ($data->get($value->getId() . 'stat') == 'Pedido de mudança') {
                        $value->setStatus("pedido de mudança");
                        $em->flush();

                        $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '10']);
                        $email = new  Email();
                        $email->setMudancas($mud);
                        $email->setSendTo($mud->getAreaResp()->getManager());
                        $email->setSendBy($person);
                        $email->setTitle($emailConfigSoftware->getSubjectMessage());
                        $email->setBody($emailConfigSoftware->getTitleOfMessage());
                        $em->persist($email);
                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $data->get($value->getId() . 'de'),fase:$task);
                    }
                }
            return $this->redirectToRoute('app_software_gestor_test', ['id' => $id]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    function compareSteps($step1, $step2)
    {
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
                if ($value->getApproveSol() == 'Approvar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {

                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    array_push($s, $values);
                }
            }

            $formDevs = $this->createForm(MudancasSoftwareDevsType::class, $muds);
            $formDevs->handleRequest($request);

            if ($formDevs->isSubmitted() && $formDevs->isValid()) {
                $developers = $muds->getDevelopers();
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
                return $this->redirectToRoute('app_software_gestor_steps', ['id' => $id]);
            }

            $imp = true;
            $haveArq = true;
            foreach ($s as $key => $value) {
                if ($value->getStatus() != 'implementado' && $value->getStatus()  != 'reprovado'  && $value->getStatus()  != 'não implementado') {
                    $imp = false;
                }


                if ($value->getAriquivo()->getApproveSol()  != 'Reprovar') {
                    $haveArq = false;
                }
            }

            $formApp = $this->createForm(MudancasGestorSoftwareAppType::class, $muds);
            $formApp->handleRequest($request);
            if ($formApp->isSubmitted() && $formApp->isValid()) {
                $mud->setComGest($muds->getCommentGestor());
                $mud->setAppGest($muds->getAppGestor());

                date_default_timezone_set("America/Sao_Paulo");
                $time = new \DateTime();
                $mud->setDateAG($time);
                $em->flush();
            }

            $formImp = $this->createForm(MudancasgestorImpType::class, $mud);
            $formImp->handleRequest($request);
            if ($formImp->isSubmitted() && $formImp->isValid()) {
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
            }

            $ia = $em->getRepository(IA::class)->find(1);
            return $this->render('software/gestor/steps.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $sd,
                'formDevs' => $formDevs,
                'formApp' => $formApp,
                'formImp' => $formImp,
                'imp' => $imp,
                'step' => $s,
                'iaPerm' => $ia->HeIsPerm($person),
                'haveArq' => $haveArq
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    
    /**
     * generate tasks AI from description project
     * @Route("/software/gestor/IA/fases/{id}", name="app_software_gestor_IA_fases")
     * @return Response
     */
    public function IAfases(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = $request->getSession();
        if ($session->get('token_jwt') === '') {
            return $this->redirectToRoute('app_login');
        }
        $em = $doctrine->getManager();
        $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
        $ia = $em->getRepository(IA::class)->find(1);
        if ($ia->HeIsPerm($person)) {
            $mud = $em->getRepository(Mudancas::class)->find($id);
            if (!$mud) {
                // Handle case when the Mudancas entity with the given $id is not found.
                // You might want to return an appropriate response or redirect.
                return new Response('Mudancas not found', 404);
            }
            $data = $request->request; 
            $input = $data->get(strval($id) . 'desc');
            $words = str_word_count($input);

            if ($words > 10) {

                try {
                $client = new GeminiAPIClient('AIzaSyCsf41aD_WbpzTYYSIzLN4aBOcrspiCQpM');
                $response = $client->geminiPro()->generateContent(
                    new TextPart($input . '.,develop the idea and generate To-Do List in number point , in Portuguese , '.$data->get('c5')),
                );

                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                // Add a section to the document
                $section = $phpWord->addSection();
                // Add a title

                // Add an image
                $imagePath = 'serdia_logo.png';
                $section->addImage($imagePath, array('width' => 200, 'height' => 40, 'marginTop' => 10, 'marginLeft' => 10));
                $section->addText('Documentação gerada por IA', array('size' => 16, 'bold' => true));
                $section->addTextBreak();

                preg_match_all('/(\d+)\.\s*(.+)/', $response->text(), $matches, PREG_SET_ORDER);
                $todoList = [];
                foreach ($matches as $match) {
                    $todoList[$match[1]] = trim($match[2]);
                }
                // Add text to the section
                $array = explode("\n", $response->text());
                foreach ($array as $value) {
                    // Regular expression to find text between **
                    $pattern = '/\*\*(.*?)\*\*/';
                    preg_match_all($pattern, $value, $matches);

                    // Replace ** with empty string and apply bold formatting
                    foreach ($matches[0] as $match) {
                        $replace = str_replace('**', '', $match);
                        $value = str_replace($match, $replace, $value);
                    }

                    // Add the modified text to the document
                    $section->addText($value);
                    // Add a new line
                    $section->addTextBreak();
                }


                $mud = $em->getRepository(Mudancas::class)->find($id);
                $muds = $mud->getMudS();
                //steps Gestor 
                $sd = [];
                $SD =  $muds->getStepsGestor();
    
                foreach ($SD as $key => $value) {
                    # code...
                    if ($value->getApproveSol() == 'Approvar') {
                        array_push($sd, $value);
                    }
                }


                preg_match_all('/(\d+)\.\s*(.+)/', $response->text(), $matches, PREG_SET_ORDER);

                $pattern = '/\d+\.\s*(.*)/';
                preg_match_all($pattern, $response->text(), $matches);
                
                $array = $matches[1];
                
              

                $filename = 'IASteps'.$id.'.docx';
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($filename);

                $publicDirectory = $this->getParameter('kernel.project_dir');
                $excelFilepath = $publicDirectory . '/public/' . $filename;
                $title = "";
                $comments = "";
                foreach ($array as $key => $value) {
                    
                    $parts = explode(':', $value, 2);
                    $step = new Steps();
                    $step->setTitle($parts[0]);
                    $step->setComments($parts[1]);
                    $step->setAriquivo($sd[0]);
                    $step->setIa('ia');
                    date_default_timezone_set("America/Sao_Paulo");
                    $time = new \DateTime();
                    $step->setDateCreation($time->format('Y-m-d H:i:s'));
                    if($step->getComments()!=''){
                    $em->persist($step);
                    $em->flush();}
                }
                return $this->redirectToRoute('app_software_gestor_steps', ['id' => $id]);

                //code...
            } catch (\Throwable $th) {
                return $this->redirectToRoute('app_software_gestor_steps', ['id' => $id]);
            }
            } else {
                echo "A entrada deve conter mais de 10 palavras.";
                return $this->redirectToRoute('app_software_gestor_steps', ['id' => $id]);
            }
        } else {
            return $this->redirectToRoute('app_software_gestor_steps', ['id' => $id]);
        }
    }

    /**
     * generate tasks AI from description project
     * @Route("/software/gestor/IA/deletestepIa/{id}/{ids}", name="app_software_gestor_IA_deletestepIa")
     * @return Response
     */
    public function IAdeletestepIa(ManagerRegistry $doctrine, Request $request, $id,$ids): Response
    {
        $session = $request->getSession();
        if ($session->get('token_jwt') === '') {
            return $this->redirectToRoute('app_login');
        }
        $em = $doctrine->getManager();
        $step = $em->getRepository(Steps::class)->find($id);
        $em->remove($step);
        $em->flush();
         
        return $this->redirectToRoute('app_software_gestor_steps', ['id' => $ids]);
    }
    
    
    /**
     * generate tasks AI from description project
     * @Route("/software/gestor/IA/downIA/{id}", name="downIA")
     * @return Response
     */
    public function downIA(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $filename = 'IASteps'.$id.'.docx';

            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath = $publicDirectory . '/public/' . $filename;

            $response = new BinaryFileResponse($excelFilepath);
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );

            return $response;
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
        $developers = $muds->getDevelopers();
        for ($i = 1; $i <= sizeof($data) / 4; $i++) {
            $stepsGestor = $em->getRepository(StepsGestor::class)->find($data->get($i . 'a'));
            $steps = new Steps();
            $steps->setAriquivo($stepsGestor);
            $steps->setComments($data->get(strval($i) . 'desc'));
            $steps->setDateCreation($data->get(strval($i) . 'date'));
            $steps->setTitle($data->get(strval($i)));

            if (sizeof($muds->getDevelopers()) >= 0) {
                foreach ($muds->getDevelopers() as $key => $value) {
                    $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '6']);
                    $email = new  Email();
                    $email->setMudancas($mud);
                    $email->setSendTo($value);
                    $email->setSendBy($person);
                    $email->setTitle($emailConfigSoftware->getSubjectMessage());
                    $email->setBody($emailConfigSoftware->getTitleOfMessage());
                    $em->persist($email);
                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $data->get(strval($i) . 'desc'));
                }
            }

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


    /**
     * generate documentations AI from description project
     * @Route("/software/gestor/IA/steps/{id}", name="app_software_gestor_IA_steps")
     * @return Response
     */
    public function IASteps(ManagerRegistry $doctrine, Request $request, $id): Response
    {
       $session = $request->getSession();
        if ($session->get('token_jwt') === '') {
            return $this->redirectToRoute('app_login');
        }
        $em = $doctrine->getManager();
        $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
        $ia = $em->getRepository(IA::class)->find(1);
        if ($ia->HeIsPerm($person)) {
            $mud = $em->getRepository(Mudancas::class)->find($id);
            if (!$mud) {
                // Handle case when the Mudancas entity with the given $id is not found.
                // You might want to return an appropriate response or redirect.
                return new Response('Mudancas not found', 404);
            }
            $data = $request->request; 
            $input = $data->get(strval($id) . 'desc');
            $words = str_word_count($input);

            if ($words > 10) {

                $client = new GeminiAPIClient($ia->getApiToken());
                $response = $client->geminiPro()->generateContent(
                    new TextPart($input . '., develope the idea and generate description more details'.$data->get('c1').' '.$data->get('c2').'   '.$data->get('c3').'  '.$data->get('c4').'  , in  portuguese , '.$data->get('c5')),
                );

                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                // Add a section to the document
                $section = $phpWord->addSection();
                // Add a title

                // Add an image
                $imagePath = 'serdia_logo.png';
                $section->addImage($imagePath, array('width' => 200, 'height' => 40, 'marginTop' => 10, 'marginLeft' => 10));
                $section->addText('Documentação gerada por IA', array('size' => 16, 'bold' => true));
                $section->addTextBreak();
                // Add text to the section
                $array = explode("\n", $response->text());
                foreach ($array as $value) {
                    // Regular expression to find text between **
                    $pattern = '/\*\*(.*?)\*\*/';
                    preg_match_all($pattern, $value, $matches);

                    // Replace ** with empty string and apply bold formatting
                    foreach ($matches[0] as $match) {
                        $replace = str_replace('**', '', $match);
                        $value = str_replace($match, $replace, $value);
                    }

                    // Add the modified text to the document
                    $section->addText($value);
                    // Add a new line
                    $section->addTextBreak();
                }

                $filename = 'IADoc'.$id.'.docx';
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($filename);

                $publicDirectory = $this->getParameter('kernel.project_dir');
                $excelFilepath = $publicDirectory . '/public/' . $filename;

                $response = new BinaryFileResponse($excelFilepath);
                $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    'hello_word.docx'
                );

                return $response;
            } else {
                echo "A entrada deve conter mais de 10 palavras.";
                return $this->redirectToRoute('app_software_gestor_documentation', ['id' => $id]);
            }
        } else {
            return $this->redirectToRoute('app_software_gestor_documentation', ['id' => $id]);
        }
    }



    /**
     * Renders the changeRequest TI page for the GestorController.
     *
     * @Route("/software/gerente/changeRequest/{id}", name="app_software_gestor_changeRequest")
     * @return Response
     */
    public function changeRequest(ManagerRegistry $doctrine, Request $request, $id): Response
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
                if ($value->getApproveSol() == 'Approvar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "pedido de mudança") {
                        array_push($s, $values);
                    }
                }
            }


            return $this->render('software/gestor/changeRequest.html.twig', [
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
     * Renders the changeRequest TI page for the Gerente .
     *
     * @Route("/software/gerente/changeRequest/approve/{id}", name="app_software_gestor_changeRequest_approve")
     * @return Response
     */
    public function changeRequestApprove(ManagerRegistry $doctrine, Request $request, $id): Response
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

            $doc = null;
            foreach ($SD as $key => $value) {
                # code...
                if ($value->getApproveSol() == 'Approvar') {
                    array_push($sd, $value);
                    $doc = $value;
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "pedido de mudança") {
                        array_push($s, $values);
                    }
                }
            }

            $data = $request->request;
            for ($i = 1; $i <= sizeof($data) / 4; $i++) {
                foreach ($s as $key => $value) {
                    if ($data->get($value->getId() . 'stat') == 'Approvar') {
                        foreach ($SD as $key => $values) {
                            # code...
                            if ($values->getApproveSol() == 'Approvar') {
                                $values->setApproveSol('Reprovar');
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                $formattedTime = $time->format('Y-m-d H:i:s');
                                $values->setDateClientRep($formattedTime);


                                $em->flush();
                            }
                        }
                        $value->setStatus("aguardando implementação");
                        $em->flush();
                        $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '11']);
                        $email = new  Email();
                        $email->setMudancas($mud);
                        $email->setSendTo($mud->getMangerMudancas());
                        $email->setSendBy($person);
                        $email->setTitle($emailConfigSoftware->getSubjectMessage());
                        $email->setBody($emailConfigSoftware->getTitleOfMessage());
                        $em->persist($email);
                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $data->get($value->getId() . 'de'));
                    } elseif ($data->get($value->getId() . 'stat') == 'Reprovar') {
                        $value->setStatus("reprovado");
                        $em->flush();

                        $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '12']);
                        $email = new  Email();
                        $email->setMudancas($mud);
                        $email->setSendTo($mud->getAddBy());
                        $email->setSendBy($person);
                        $email->setTitle($emailConfigSoftware->getSubjectMessage());
                        $email->setBody($emailConfigSoftware->getTitleOfMessage());
                        $em->persist($email);
                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $data->get($value->getId() . 'de'));
                    }
                }
            }
            return $this->redirectToRoute('app_software_gestor_changeRequest', ['id' => $id]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }


    public function sendEmail(
        ManagerRegistry $doctrine,
        Request $request,
        $sendTo,
        $mud,
        $per,
        $demand,
        $gestor,
        $client = null,
        $com = null,
        $fase = null
    ) {

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
            if ($com == null) {
                $com = "null";
            }
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
                    'fase' => $fase,
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
                    'fase' => $fase,
                    'demand'    =>  $demand,
                    'com' => $com
                ]));
            }

            // Envia o e-mail
            $mail->Send();
            return $mail;

            return $this->redirectToRoute('app_mudancas');
        } catch (Exception $e) {
            //   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        return;
    }
}
