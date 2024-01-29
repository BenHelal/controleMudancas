<?php

namespace App\Controller\Software;

use App\Entity\ApiToken;
use App\Entity\ConfigEmail;
use App\Entity\Email;
use App\Entity\EmailToSendConfig;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\StepsGestor;
use App\Model\Class\IpAdress;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class SolicitanteController extends AbstractController
{



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
            if ($SD != null) {
                $publicDirectory = $this->getParameter('kernel.project_dir');
                $excelFilepath2 =  $publicDirectory . '/public/assets/' . $mud->getId() . '/documentation';
                try {
                    //code...
                    $files = scandir($excelFilepath2);
                    $files = array_diff($files, ['.', '..']);
                } catch (\Throwable $th) {
                    //throw $th;
                    $files = "";
                }
            } else {
                $files = "";
            }
            return $this->render('software/solicitante/documentation.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $SD,
                'files' => $files
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }



    /**
     * Renders the documentation page for the solicitanteController.
     *
     * @Route("/software/solicitante/app/documentation/{id}", name="app_software_sol_approve_documentation")
     * @return Response
     */
    public function approve(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $muds = $mud->getMudS();
            $data = $request->request;
            $SD =  $muds->getStepsGestor();
            // You can loop through all the parameters in the InputBag:
            foreach ($data->all() as $key => $value) {
                for ($i = 0; $i < sizeof($SD); $i++) {
                    if ($key == $SD[$i]->getId() . 'stat') {
                        if (($value == 'Aprovar' || $value == 'Reprovar')) {
                            $sd = $em->getRepository(StepsGestor::class)->find($SD[$i]->getId());
                            $sd->setApproveSol($value);
                            // Repeated logic for handling both 'file' and 'files'
                            if ($request->files->get('1files') != null) {
                                $fileName = $sd->getId() . '_' . $muds->getId() . '.' . $request->files->get('1files')->guessExtension();
                                $publicDirectory = $this->getParameter('kernel.project_dir');
                                $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
                                $request->files->get('1files')->move($excelFilepath, $fileName);
                                $sd->setDocClient($fileName);
                            }
                            $em->flush();
                            if ($value == 'Aprovar') {

                                $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '4']);
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle($emailConfigSoftware->getSubjectMessage());
                                $email->setBody($emailConfigSoftware->getTitleOfMessage());
                                $em->persist($email);
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $SD[$i]->getId() . 'de');
                            } else {
                                $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '5']);
                                $email = new  Email();
                                $email->setMudancas($mud);
                                $email->setSendTo($mud->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle($emailConfigSoftware->getSubjectMessage());
                                $email->setBody($emailConfigSoftware->getTitleOfMessage());
                                $em->persist($email);
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false, com: $SD[$i]->getId() . 'de');
                            }
                            return $this->redirectToRoute('app_software_sol_documentation', ['id' => $id]);
                        }
                    }
                }
            }
        } else {
            return $this->redirectToRoute('app_login');
        }
    }


    public function sendEmail(ManagerRegistry $doctrine, Request $request, $sendTo, $mud, $per, $demand,  $gestor, $client = null, $com = null)
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

        if ($com == null) {
            $com = " ";
        }
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
                    'com' => $com,
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

    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/solicitante/test/{id}", name="app_software_solicitante_test")
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
                if ($value->getApproveSol() == 'Aprovar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "teste usario") {
                        array_push($s, $values);
                    }
                }
            }


            return $this->render('software/solicitante/test.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'solicitanteController',
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
     * @Route("/software/solicitante/test/approve/{id}", name="app_software_solicitante_test_approve")
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

            //steps solicitante 
            $sd = [];
            $s = [];
            $SD =  $muds->getStepsGestor();


            foreach ($SD as $key => $value) {
                # code...
                if ($value->getApproveSol() == 'Aprovar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "teste usario") {
                        array_push($s, $values);
                    }
                }
            }

            $data = $request->request;
            for ($i = 1; $i <= sizeof($data) / 4; $i++) {
                foreach ($s as $key => $value) {

                    if ($request->files->get(strval($value->getId()) . 'files') != null) {
                        $fileName = $value->getId() . '_TEST_Cliente_' . $muds->getId() . '.' . $request->files->get(strval($value->getId()) . 'files')->guessExtension();
                        $publicDirectory = $this->getParameter('kernel.project_dir');
                        $excelFilepath =  $publicDirectory . '/public/assets/' . $mud->getId();
                        $request->files->get(strval($value->getId()) . 'files')->move($excelFilepath, $fileName);
                        $value->setDocClient($fileName);
                    }
                    if ($data->get($value->getId() . 'stat') == 'Aprovar') {
                        $value->setStatus("aguardando implantação");
                        $em->flush();
                        
                    } elseif ($data->get($value->getId() . 'stat') == 'Reprovar') {
                        $value->setStatus("teste ti");
                        $em->flush();

                        $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '7']);
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
            return $this->redirectToRoute('app_software_solicitante_test', ['id' => $id]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }



    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/solicitante/imp/{id}", name="app_software_solicitante_imp")
     * @return Response
     */
    public function imp(ManagerRegistry $doctrine, Request $request, $id): Response
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
                if ($value->getApproveSol() == 'Aprovar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "aguardando implantação") {
                        array_push($s, $values);
                    }
                }
            }

            return $this->render('software/solicitante/imp.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'solicitanteController',
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
     * @Route("/software/solicitante/imp/approve/{id}", name="app_software_solicitante_imp_approve")
     * @return Response
     */
    public function impApprove(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {


            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $muds = $mud->getMudS();

            //steps solicitante 
            $sd = [];
            $s = [];
            $SD =  $muds->getStepsGestor();


            foreach ($SD as $key => $value) {
                # code...
                if ($value->getApproveSol() == 'Aprovar') {
                    array_push($sd, $value);
                }
            }

            foreach ($SD as $keys => $val) {
                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    if ($values->getStatus() == "aguardando implantação") {
                        array_push($s, $values);
                    }
                }
            }

            $data = $request->request;
            for ($i = 1; $i <= sizeof($data) / 4; $i++) {
                foreach ($s as $key => $value) {
                    if ($data->get($value->getId() . 'stat') == 'Aprovar') {
                        $value->setStatus("implantado");
                        $em->flush();

                        $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '13']);
                        $email = new  Email();
                        $email->setMudancas($mud);
                        $email->setSendTo($mud->getMangerMudancas());
                        $email->setSendBy($person);
                        $email->setTitle($emailConfigSoftware->getSubjectMessage());
                        $email->setBody($emailConfigSoftware->getTitleOfMessage());
                        $em->persist($email);
                        $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false,);
                      
                    } elseif ($data->get($value->getId() . 'stat') == 'Reprovar') {
                        $value->setStatus("pedido de mudança");
                        $em->flush();
                    }
                }
            }
            return $this->redirectToRoute('app_software_solicitante_test', ['id' => $id]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
