<?php

namespace App\Controller\Software;

use App\Entity\ApiToken;
use App\Entity\ConfigEmail;
use App\Entity\Email;
use App\Entity\TokenData;
use App\Model\Class\IpAdress;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class GerenteController extends AbstractController
{

    #[Route('/software/gerente', name: 'app_software_gerente')]
    public function index(): Response
    {
        return $this->render('software/gerente/index.html.twig', [
            'controller_name' => 'GerenteController',
        ]);
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
            $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mud]);

            $sps = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
            $number_sector_app = 0;
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

          
            if ($form->isSubmitted() && $form->isValid()) {
                if ($request->request->get('1test') == 'Sim') {
                    $mud->setAppMan(1);
                    //email System 
                    if ($mud->getAppMan() == 1) {
                        /**
                         * Send Email
                         * -------------------------------------------------------
                         **/

                        if ($mud->getClient() != null) {

                            if ($mud->getClient()->getRespEmail() != null) {

                                # code...
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
                                //dd($email);
                                $em->persist($email);
                                $em->persist($tok);
                                $em->flush();
                                if ($token != null) {
                                    //143.255.163.142
                                    //10.100.2.61:25020
                                    $url = "10.100.2.61/ClientExteranlAcces/public/add/data/token";
                                    //The data you want to send via POST
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
                                } else {
                                    $cl = null;
                                }
                                
                                $this->sendEmail($doctrine, $request, $mud->getClient(), $mud, $person, 'client', false, $mud->getClient());
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

                        if ($mud->getNansenNumber() == null) {
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($mud->getAddBy());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovado pelo Gerente do solicitante');
                            $email->setBody('gerenteAPP');
                            $em->persist($email);
                            $em->flush();
                            $this->sendEmail($doctrine, $request, $email->getSendTo(), $email->getMudancas(), $email->getSendBy(), $email->getBody(), false);
                        } else {
                            $email = new  Email();
                            $email->setMudancas($mud);
                            $email->setSendTo($mud->getAddBy());
                            $email->setSendBy($person);
                            $email->setTitle('Aprovado pelo Gerente do solicitante');
                            $email->setBody('gerenteAPP');
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

                        /**
                         * check if rejected 
                         * so will reject for all the other area managed by the same person 
                         */
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



                    if ($request->request->get('2test') != null) {
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
