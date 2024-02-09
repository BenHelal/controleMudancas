<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\ConfigEmail;
use App\Entity\Email;
use App\Entity\EmailToSendConfig;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Process;
use App\Entity\Requestper;
use App\Entity\SectorProcess;
use App\Form\SectorProcessType;
use App\Model\Class\IpAdress;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
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


            if($mudancas->getMangerMudancas() == null){
                return $this->redirectToRoute('upm', ['id' => $mudancas->getId()]);
            }else{
                if ($mudancas->getAppMan() == null ) {
                    return $this->redirectToRoute('upm', ['id' => $mudancas->getId()]);
                }
            }

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
            foreach ($sps as $key => $sp) {
                if ($sp->getSector()->getCoordinator() == $person ) {
                    $oneOfSp = $sp;
                }
            }
            
            if ($oneOfSp  != null) {
                $form = $this->createForm(SectorProcessType::class, $oneOfSp);
            } else {
                $form = $this->createForm(SectorProcessType::class, $sps[0]);
            }
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $d = $request->get('sector_process');
                
                $isTheLastApprove = true;
                if($mudancas->getMudS() == null){
                    $isTheLastApprove = false;
                }  

                foreach ($sps as $key => $sp) {
                    
                    if ($sp->getSector()->getCoordinator() == $person ) {
                        //if( $mudancas->getManager)
                        if ($oneOfSp != null) {
                            $sp->setComment($d['comment']);
                            $sp->setAppSectorMan($d['appSectorMan']);
                            date_default_timezone_set("America/Sao_Paulo");
                            $time = new \DateTime();
                            $sp->setDataCreation($time);
                        }
                        $em->persist($sp);
                        $em->flush();    
                    }

                    if($sp->getAppSectorMan() != 1 ){
                        $isTheLastApprove = false;
                    }
                }
                

                if($oneOfSp->getAppSectorMan() == 1){
                    $email = new  Email();
                    $email->setMudancas($mudancas);
                    $email->setSendTo($mudancas->getAddBy());
                    $email->setSendBy($person);
                    $email->setTitle('Aprovado pelo Gerente do solicitante');
                    $email->setBody('app_areaImp');
                    $em->persist($email);
                    $em->flush();
                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                    
                }else{
                    
                    $mudancas->setImplemented(2);
                    if($mudancas->getNansenNumber() != null){
                        
                    $mudancas->setManagerUserAdd($person);
                    
                         }$mudancas->setDone('Feito');
                    $email = new  Email();
                    $email->setMudancas($mudancas);
                    $email->setSendTo($mudancas->getAddBy());
                    $email->setSendBy($person);
                    $email->setTitle('Aprovado pelo Gerente do solicitante');
                    $email->setBody('reject_areaImp');
                    $em->persist($email);
                    $em->flush();
                    $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);           
                }

                if($isTheLastApprove){
                    $emailConfigSoftware = $em->getRepository(EmailToSendConfig::class)->findOneBy(['titleOfMessage' => '1']);
                                $email = new  Email();
                                $email->setMudancas($mudancas);
                                $email->setSendTo($mudancas->getAddBy());
                                $email->setSendBy($person);
                                $email->setTitle($emailConfigSoftware->getSubjectMessage());
                                $email->setBody($emailConfigSoftware->getTitleOfMessage());
                                $em->persist($email);
                                $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                            
                }

                return $this->redirectToRoute('upm', ['id' => $id]);
                
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


    public function sendEmail(
        ManagerRegistry $doctrine,
        Request $request,
        $sendTo,
        $mud,
        $per,
        $demand,
        $gestor,
        $client = null,
        $com = null
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
                    'demand'    =>  $demand,
                    'com' => $com
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
