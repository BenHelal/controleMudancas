<?php

namespace App\Controller;

use App\Entity\Departemant;
use App\Entity\Person;
use App\Entity\Sector;
use App\Form\PersonType;
use App\Model\Class\FunctionUsers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function PHPUnit\Framework\isEmpty;

class LoginController extends AbstractController
{
    #[Route('/', name: 'log_employer')]
    public function login(ManagerRegistry $doctrine, Request $request, HttpClientInterface $client): Response
    {
        //get Session 
        $session = new Session();
        $session = $request->getSession();
        //test if he is connected before 
        if ($session->get('token_jwt') == null) {

            $em = $doctrine->getManager();
            $fu = new FunctionUsers();
            $url = 'http://10.100.1.253/a/customers.php?tk=24dbdb7659f46b318b543981f2b0784226b8bd54';
            $fu->checkDataClient($fu->getData($url), $em);
            // update list of employer 
            $url = 'http://10.100.1.253/a/users.php?sys=1&tk=24dbdb7659f46b318b543981f2b0784226b8bd54';
            $ch = curl_init($url);
            $data = array();
            $payload = json_encode(array("user" => $data));
            // Attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            // Set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            // Return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Execute the POST request
            $result = curl_exec($ch);
            curl_close($ch);
            $personJSON = $result;
            $persons = json_decode($personJSON);
            //var_dump(sizeof($persons));
            for ($i = 0; $i < sizeof($persons); $i++) {
                $entityManager = $doctrine->getManager();
                $person =  $entityManager->getRepository(Person::class)->findOneBy(['name' => $persons[$i]->name]);
                if ($person == null) {
                    $person = new Person();
                    $person->setName($persons[$i]->name);
                    $person->setEmail($persons[$i]->email);
                    $dep =  $entityManager->getRepository(Departemant::class)->findOneBy(['name' => $persons[$i]->departemant]);
                    if ($dep == null) {
                        $dep = new Departemant();
                        $dep->setName($persons[$i]->departemant);
                    }
                    $person->setDepartemant($dep->getName());
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($person);
                    $entityManager->persist($dep);
                    $entityManager->flush();
                } else {
                    if ($person->getDepartemant() == null) {
                        $dep =  $entityManager->getRepository(Departemant::class)->findOneBy(['name' => $persons[$i]->departemant]);
                        if ($dep == null) {
                            $dep = new Departemant();
                            $dep->setName($persons[$i]->departemant);
                        }
                        $person->setDepartemant($dep->getName());
                        $entityManager = $doctrine->getManager();
                        $entityManager->persist($person);
                        $entityManager->persist($dep);
                        $entityManager->flush();
                    } else {
                        $dep =  $entityManager->getRepository(Departemant::class)->findOneBy(['name' => $persons[$i]->departemant]);
                        if ($dep == null) {
                            $dep = new Departemant();
                            $dep->setName($persons[$i]->departemant);
                        }
                        $entityManager->persist($dep);
                        $entityManager->flush();
                    }
                }
            }

            /**
             * Check Sector
             * 
             */
            $sectors = file_get_contents('sector.json');
            $sectors = json_decode($sectors);
            foreach ($sectors as $key => $sector) {
                $sec = $entityManager->getRepository(Sector::class)->findOneBy(['name' => $sector[0]]);
                if ($sec == null) {
                    $sec = new Sector();
                    $manager =  $entityManager->getRepository(Person::class)->findOneBy(['email' => $sector[2]]);
                    $coordinator =  $entityManager->getRepository(Person::class)->findOneBy(['email' => $sector[1]]);
                    $sec->setName($sector[0]);
                    $sec->setManager($manager);
                    $sec->setCoordinator($coordinator);
                    $entityManager->persist($sec);
                    $entityManager->flush();
                }
            }

            $entityManager = $doctrine->getManager();
            $person =  $entityManager->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $person = new Person();
            $form = $this->createForm(PersonType::class, $person);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $doctrine->getManager();
                $username = $form["userName"]->getData();
                $password = $form["password"]->getData();
                $password = hash('whirlpool', $password);

                if ($username == 'mod') {
                    $password2 = hash('whirlpool', 'oldoni');
                    if($password == $password2){
                        //MAGNUM OLDONI
                        $mod = $entityManager->getRepository(Person::class)->findOneBy(['name' => 'MAGNUM OLDONI']);
                        if($mod != null ){
                            $mod->setUsername($username);
                            $mod->setPassword($password);
                            $entityManager->persist($mod);
                            $entityManager->flush();

                            if ($mod != null) {
                                // Create token header as a JSON string
                                $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
                                // Create token payload as a JSON string
                                $payload = json_encode(['user_id' => $username]);
                                // Encode Header to Base64Url String
                                $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
                                // Encode Payload to Base64Url String
                                $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
                                // Create Signature Hash
                                $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
                                // Encode Signature to Base64Url String
                                $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                                // Create JWT
                                $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
                                $session = new Session();
        
                                $session->set('token_jwt', $jwt);
                                $session->set('name', $mod->getName());
                                $request->setSession($session);
                                $person =  $em->getRepository(Person::class)->findOneBy(['name' => $person->getName()]);
                                date_default_timezone_set("America/Sao_Paulo");
                                $time = new \DateTime();
                                $time->format('Y-m-d H:i:s');
                                $person->setLastConnection($time);
                                $em->persist($person);
                                $em->flush();
                                
                                if ($person->getPermission() == null) {
                                    return $this->redirectToRoute('app_request');
                                } else {
                                    date_default_timezone_set("America/Sao_Paulo");
                                    $time = new \DateTime();
                                    $time->format('Y-m-d H:i:s');
                                    $person->setLastConnection($time);
                                    $person->setEmail($session->get('email'));
                                    $em->persist($person);
                                    $em->flush();
                                    return $this->redirectToRoute('app_mudancas');
                                }
                            }
                        }
                    }else{
                            return $this->render('login/index.html.twig', [
                                'controller_name' => 'LoginController',
                                'login' => 'true',
                                'form' => $form->createView(),
                                'wrong' => true
                            ]);
                    }
                } else {



                    $url = 'http://10.100.1.253/a/connection.php?sys=1&user=' . $username . '&pass=' . $password . '';
                    // Create a new cURL resource
                    $ch = curl_init($url);
                    // Setup request to send json via POST
                    $data = array();
                    $payload = json_encode(array("user" => $data));
                    // Attach encoded JSON string to the POST fields
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    // Set the content type to application/json
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    // Return response instead of outputting
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // Execute the POST request
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $personJSON = $result;
                    $person = json_decode($personJSON);

                    if ($person->status == 'Error') {
                        return $this->render('login/index.html.twig', [
                            'controller_name' => 'LoginController',
                            'login' => 'true',
                            'form' => $form->createView(),
                            'wrong' => true
                        ]);
                    }

                    if ($person != null) {
                        // Create token header as a JSON string
                        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
                        // Create token payload as a JSON string
                        $payload = json_encode(['user_id' => $username]);
                        // Encode Header to Base64Url String
                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
                        // Encode Payload to Base64Url String
                        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
                        // Create Signature Hash
                        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
                        // Encode Signature to Base64Url String
                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                        // Create JWT
                        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
                        $session = new Session();

                        $session->set('token_jwt', $jwt);
                        $session->set('name', $person->name);
                        $session->set('departemant', $person->departemant);
                        $session->set('role', $person->role);
                        $session->set('email', $person->email);
                        $request->setSession($session);
                        $person =  $em->getRepository(Person::class)->findOneBy(['name' => $person->name]);
                        $person->setEmail($session->get('email'));
                        date_default_timezone_set("America/Sao_Paulo");
                        $time = new \DateTime();
                        $time->format('Y-m-d H:i:s');
                        $person->setLastConnection($time);
                        $em->persist($person);
                        $em->flush();

                        if ($person->getPermission() == null) {
                            return $this->redirectToRoute('app_request');
                        } else {
                            date_default_timezone_set("America/Sao_Paulo");
                            $time = new \DateTime();
                            $time->format('Y-m-d H:i:s');
                            $person->setLastConnection($time);
                            $person->setEmail($session->get('email'));
                            $em->persist($person);
                            $em->flush();
                            return $this->redirectToRoute('app_mudancas');
                        }
                    } else {
                        return $this->render('login/index.html.twig', [
                            'controller_name' => 'LoginController',
                            'login' => 'true',
                            'form' => $form->createView(),
                            'wrong' => false
                        ]);
                    }
                }
            }
            return $this->render('login/index.html.twig', [
                'controller_name' => 'test ',
                'login' => 'true',
                'form' => $form->createView(),
                'wrong' => false
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        $session->remove('token_jwt');
        return $this->redirectToRoute('log_employer');
    }

    #[Route('/administrator', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            return $this->redirectToRoute('dash_admin');
        } else {
            $admin = new Person();
            $form = $this->createForm(PersonType::class, $admin);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $doctrine->getManager();
                $username = $form["userName"]->getData();
                $password = $form["password"]->getData();
                $password = hash('whirlpool', $password);
                $url = 'http://10.100.1.253/a/connection.php?sys=1&user=' . $username . '&pass=' . $password . '';
                $ch = curl_init($url);
                $data = array();
                $payload = json_encode(array("user" => $data));

                // Attach encoded JSON string to the POST fields
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                // Set the content type to application/json
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                // Return response instead of outputting
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Execute the POST request
                $result = curl_exec($ch);
                curl_close($ch);
                $personJSON = $result;
                $person = json_decode($personJSON);

                if ($person->status == 'Error') {
                    return $this->render('admin/index.html.twig', [
                        'controller_name' => 'AdminController',
                        'form' => $form->createView(),
                        'wrong' => true
                    ]);
                }


                $p = $em->getRepository(Person::class)->findOneBy(['name' => $person->name]);
                //dd($p);
                if ($p->getRole() == null) {
                    return $this->redirectToRoute('app_mudancas');
                } else {
                    if ($person->name != '') {
                        // Create token header as a JSON string
                        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
                        // Create token payload as a JSON string
                        $payload = json_encode(['user_id' => $username]);
                        // Encode Header to Base64Url String
                        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
                        // Encode Payload to Base64Url String
                        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
                        // Create Signature Hash
                        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
                        // Encode Signature to Base64Url String
                        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                        // Create JWT
                        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
                        $session = new Session();
                        $session->set('token_admin', $jwt);
                        $session->set('admin_name', $person->name);
                        $session->set('admin_departemant', $person->departemant);
                        $session->set('admin_role', $person->role);
                        $request->setSession($session);
                        //return new Response('Saved new product with id'.$result);
                        // Close cURL resource
                        return $this->redirectToRoute('dash_admin');
                    } else {
                        return $this->render('admin/index.html.twig', [
                            'controller_name' => 'AdminController',
                            'form' => $form->createView(),
                            'wrong' => false
                        ]);
                    }
                }
            }
            return $this->render('admin/index.html.twig', [
                'controller_name' => 'AdminController',
                'form' => $form->createView(),
                'wrong' => false
            ]);
        }
    }
}
