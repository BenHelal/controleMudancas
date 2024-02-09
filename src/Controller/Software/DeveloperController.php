<?php

namespace App\Controller\Software;

use App\Entity\ApiToken;
use App\Entity\ConfigEmail;
use App\Entity\Email;
use App\Entity\EmailToSendConfig;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Steps;
use App\Model\Class\IpAdress;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/dev/steps/{id}", name="app_software_devs_steps")
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
                if ($value->getApproveSol() == 'Aprovar') {
                    array_push($sd, $value);
                }
            }
            foreach ($SD as $keys => $val) {

                foreach ($val->getSteps() as $keys => $values) {
                    # code...
                    array_push($s, $values);
                }
            }
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath2 =  $publicDirectory . '/public/assets/' . $mud->getId() . '/documentation';

            $files = scandir($excelFilepath2);
            $files = array_diff($files, ['.', '..']);

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

            return $this->render('software/developer/index.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $sd,
                'step' => $s, 'files' => $filesAssociative,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
    /**
     * @Route("/update-task/", name="upadatTask")
     */
    public function upadatTask(ManagerRegistry $doctrine, Request $request)
    {

        $id = $request->query->get('id');
        $idM = $request->query->get('idm');
        $data = $request->request;
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Steps::class)->find($id);
        $task->setStartdevdate($data->get(strval($id) . 'InitDate'));
        $task->setEnddevdatet($data->get(strval($id) . 'EndDate'));


        if ($request->files->get('1files') != null) {

            $fileName = $task->getTitle() . '-' . $id . '' . $idM . '.' . $request->files->get('1files')->guessExtension();
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath = $publicDirectory . '/public/assets/' . $idM . '/dev/' . $id;
            $request->files->get('1files')->move($excelFilepath, $fileName);
            $task->setScreendevend($fileName);
        }
        $entityManager->flush();
        return $this->redirectToRoute('app_software_devs_steps', ['id' => $idM]);
    }
    /**
     * @Route("/update-task-status", name="update_task_status", methods={"POST"})
     */
    public function updateTaskStatus(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();


        // Retrieve the task ID and new status from the AJAX request
        $taskId = $request->request->get('taskId');
        $newStatus = $request->request->get('newStatus');
        $id = $request->request->get('id');
        // Fetch the task entity from the database
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Steps::class)->find($taskId);
        $person =  $entityManager->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);

        $mud = $entityManager->getRepository(Mudancas::class)->find($id);

        if($task->getStatus() != $newStatus){
            $emailConfigSoftware = $entityManager->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '7']);
            $email = new  Email();
            $email->setMudancas($mud);
            $email->setSendTo($mud->getMangerMudancas());
            $email->setSendBy($person);
            $email->setTitle($emailConfigSoftware->getSubjectMessage());
            $email->setBody($emailConfigSoftware->getTitleOfMessage());
            $entityManager->persist($email);
            $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false,);
        }

        if (!$task) {
            return new JsonResponse(['message' => 'Task not found'], 404);
        }
        date_default_timezone_set("America/Sao_Paulo");
        $time = new \DateTime();
        $formattedTime = $time->format('Y-m-d H:i:s');
        // Update the task status
        $task->setStatus($newStatus); // Assuming you have a 'status' property in your entity
        $task->setDateStatue(strval($formattedTime));
        // Persist the changes to the database
        $entityManager->persist($task);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Task status updated successfully']);
    }


    /**
     * @Route("/DiagramGantt/{id}", name="DiagramGantt")
     */
    public function DiagramGantt(ManagerRegistry $doctrine, $id, Request $request)
    {
        //
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
                    array_push($s, $values);
                }
            }
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath2 =  $publicDirectory . '/public/assets/' . $mud->getId() . '/documentation';

            $files = scandir($excelFilepath2);
            $files = array_diff($files, ['.', '..']);


            return $this->render('software/developer/DiagramGantt.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $sd,
                'step' => $s, 'files' => $files,
            ]);
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
}
