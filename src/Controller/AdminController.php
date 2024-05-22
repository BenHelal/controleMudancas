<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Client;
use App\Entity\ConfigEmail;
use App\Entity\Departemant;
use App\Entity\DepartemantMudancass;
use App\Entity\Email;
use App\Entity\EmailToSendConfig;
use App\Entity\IA;
use App\Entity\IALog;
use App\Entity\Mudancas;
use App\Entity\MudancasSoftware;
use App\Entity\Person;
use App\Entity\Requestper;
use App\Entity\Sector;
use App\Entity\Process;
use App\Entity\Projevisa;
use App\Entity\SectorProcess;
use App\Form\AddClientType;
use App\Form\AddPersonType;
use App\Form\ClientType;
use App\Form\ConfigemailType;
use App\Form\EditPersonType;
use App\Form\EmailToSendConfigType;
use App\Form\EmailType;
use App\Form\IAType;
use App\Form\ManagerType;
use App\Form\PermissionType;
use App\Form\PersonType;
use App\Form\ProjevisaType;
use App\Form\RequestadminType;
use App\Form\RequestperType;
use App\Form\SectorType;
use App\Model\Class\Excel;
use App\Model\Class\FunctionUsers;
use App\Model\Class\IpAdress;
use App\Model\Class\Sessions;
use App\Model\Class\ThemeFn;
use App\Model\Class\UsersPermission;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpParser\Node\Stmt\Else_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/adminmud')]
class AdminController extends AbstractController
{

    #[Route('/logout_admin', name: 'logoutAdmin')]
    public function logout(Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        $session->remove('token_admin');
        $session->remove('admin_name');
        $session->remove('admin_departemant');
        $session->remove('admin_role');
        return $this->redirectToRoute('app_admin');
    }

    #[Route("/logIAGenerator", name: "logIAGenerator")]
    public function logIAGenerator(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $s = $em->getRepository(Sector::class)->findOneBy(['name' => '021 – TI MATRIZ (INFRAESTRUTURA E REDE)']);
            if (($p->getDepartemant() == 'TI' && $p->getFunction()->getName() == "021 – TI MATRIZ (INFRAESTRUTURA E REDE)") || ($p = $s->getManager())) {
                // list user have permission to access to this features .
                $ia = $em->getRepository(IA::class)->find(1);
                if ($ia == null) {
                    $ia = new  IA();

                    $em->persist($ia);
                    $em->flush();
                }
                $logs = $em->getRepository(IALog::class)->findAll();
                return $this->render('admin/ia.html.twig', [
                    'controller_name' => 'AdminController',
                    'logs' => $logs,
                    'p' => $p,
                    'page' => 3
                ]);
            } else {
                return $this->redirectToRoute('dash_admin');
            }
        } else {
            return $this->redirectToRoute('logoutAdmin');
        }
    }

    #[Route("/permissionIAGenerator", name: "permissionIAGenerator")]
    public function permissionIAGenerator(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $s = $em->getRepository(Sector::class)->findOneBy(['name' => '021 – TI MATRIZ (INFRAESTRUTURA E REDE)']);
            if (($p->getDepartemant() == 'TI' && $p->getFunction()->getName() == "021 – TI MATRIZ (INFRAESTRUTURA E REDE)") || ($p = $s->getManager())) {
                // list user have permission to access to this features .
                $ia = $em->getRepository(IA::class)->find(1);
                if ($ia == null) {
                    $ia = new  IA();

                    $em->persist($ia);
                    $em->flush();
                }
                $person = $ia->getPersons();
                $arrayperson = [];
                foreach ($person as $value) {
                    array_push($arrayperson, $value->getName());
                }
                $form = $this->createForm(IAType::class, $ia);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    date_default_timezone_set("America/Sao_Paulo");
                    $time = new \DateTimeImmutable();

                    $arrayperson2 = [];
                    foreach ($ia->getPersons() as $value) {
                        array_push($arrayperson2, $value->getName());
                    }
                    $difference = array_diff($arrayperson2, $arrayperson);
                    $log = new IALog();
                    if (sizeof($difference) != 0) {
                        $log->setLog("adicionar " . implode(", ", $difference) . " à lista de permissões IA");
                    } else {
                        $difference = array_diff($arrayperson, $arrayperson2);
                        $log->setLog("exclui " . implode(", ", $difference) . " to permission list IA");
                    }
                    $log->setCreateAt($time);
                    $log->setPerson($p);
                    $em->persist($log);
                    $em->flush();
                    return $this->redirectToRoute('permissionIAGenerator');
                }
                return $this->render('admin/ia.html.twig', [
                    'controller_name' => 'AdminController',
                    'form' => $form,
                    'p' => $p,
                    'page' => 1
                ]);
            } else {
                return $this->redirectToRoute('dash_admin');
            }
        } else {
            return $this->redirectToRoute('logoutAdmin');
        }
    }

    #[Route("/permissionIAToken", name: "permissionIAToken")]
    public function permissionIAToken(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $s = $em->getRepository(Sector::class)->findOneBy(['name' => '021 – TI MATRIZ (INFRAESTRUTURA E REDE)']);
            if (($p->getDepartemant() == 'TI' && $p->getFunction()->getName() == "021 – TI MATRIZ (INFRAESTRUTURA E REDE)") || ($p = $s->getManager())) {
                // list user have permission to access to this features .
                $ia = $em->getRepository(IA::class)->find(1);
                if ($ia == null) {
                    $ia = new  IA();

                    $em->persist($ia);
                    $em->flush();
                }
                return $this->render('admin/ia.html.twig', [
                    'controller_name' => 'AdminController',
                    'p' => $p,
                    'token' => $ia->getApiToken(),
                    'page' => 2
                ]);
            } else {
                return $this->redirectToRoute('dash_admin');
            }
        } else {
            return $this->redirectToRoute('logoutAdmin');
        }
    }

    #[Route('/popup', name: 'popup')]
    public function popup(): Response
    {
        return $this->render('admin/popup.html.twig');
    }

    #[Route("/changeIAToken", name: "changeIAToken")]
    public function changeIAToken(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $s = $em->getRepository(Sector::class)->findOneBy(['name' => '021 – TI MATRIZ (INFRAESTRUTURA E REDE)']);
            if (($p->getDepartemant() == 'TI' && $p->getFunction()->getName() == "021 – TI MATRIZ (INFRAESTRUTURA E REDE)") || ($p = $s->getManager())) {
                $data = $this->authAssinar($request->get('username'), $request->get('password'), $doctrine, $request);
                $ia = $em->getRepository(IA::class)->find(1);
                if ($data) {
                    $ia->setApiToken($request->get('token'));

                    date_default_timezone_set("America/Sao_Paulo");
                    $time = new \DateTimeImmutable();

                    $log = new IALog();
                    $log->setLog("altera Token IA");
                    $log->setCreateAt($time);
                    $log->setPerson($p);
                    $em->persist($log);
                    $em->flush();
                    return $this->redirectToRoute('permissionIAToken');
                } else {
                    return $this->redirectToRoute('popup');
                }
            } else {
                return $this->redirectToRoute('dash_admin');
            }
        } else {
            return $this->redirectToRoute('logoutAdmin');
        }
    }

    #[Route('/dashboard', name: 'dash_admin')]
    public function dash(ManagerRegistry $doctrine, Request $request): Response
    {

        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            if ($p != null) {
                if ($p->getRole() == null) {
                    return $this->redirectToRoute('app_mudancas');
                } else {
                    //get All list Mudancas 
                    //get ALL list Departemant
                    $dep = $em->getRepository(Departemant::class)->findAll();
                    //get All list mudancas
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
                        if ($value->getMudS() != null) {
                            array_push($mudSoft, $value);
                        } else {

                            array_push($mudNorm, $value);
                        }
                    }
                    return $this->render('admin/dash.html.twig', [
                        'controller_name' => 'AdminController',
                        'percent' => $val,
                        'size' => $size,
                        'sizeSoft' => sizeof($mudSoft),
                        'sizeNorm' => sizeof($mudNorm),
                        'mud' => $arr,
                        'listrequest' => array_reverse($listrequest),
                        'p' => $p,
                        'mudancas' => array_reverse($mudancas),
                    ]);
                }
            } else {
                return $this->redirectToRoute('logoutAdmin');
            }
        } else {
            return $this->redirectToRoute('logoutAdmin');
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

    public function authAssinar($username, $password, $doctrine, $request)
    {

        $password = hash('whirlpool', $password);
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
            return false;
        } else {
            $session = new Session();
            $session = $request->getSession();
            if ($session->get('token_jwt') != '') {
                $em = $doctrine->getManager();
                $personSing = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
                $person =  $em->getRepository(Person::class)->findOneBy(['name' => $person->name]);
                if ($person == $personSing) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    #[Route('/request', name: 'app_request')]
    public function request(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $name = $session->get('name');
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $name]);
            $req =  $em->getRepository(Requestper::class)->findOneBy(['person' => $person]);
            if ($req != null) {
                if ($req->getApproves() == 'yes') {
                    return $this->redirectToRoute('app_mudancas');
                }
            } else {
                if ($req != null) {
                    return $this->render('admin/request.html.twig', [
                        'controller_name' => 'AdminController',
                        'sent' => 'true'
                    ]);
                } else {
                    $req = new Requestper();
                    $form = $this->createForm(RequestperType::class, $req);
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $req->setPerson($person);
                        date_default_timezone_set("America/Sao_Paulo");
                        $time = new \DateTime();
                        $time->format('Y-m-d H:i:s');
                        $req->setDate($time);
                        $em->persist($req);
                        $em->flush();
                        return $this->redirectToRoute('app_request');
                    }
                    return $this->render('admin/request.html.twig', [
                        'controller_name' => 'AdminController',
                        'form' => $form->createView(),
                        'sent' => 'false'
                    ]);
                }
            }
        } else {
            return $this->redirectToRoute('log_employer');
        }

        return $this->render('admin/request.html.twig', [
            'controller_name' => 'AdminController',
            'sent' => 'true'
        ]);
    }




    //list person 
    #[Route('/lp', name: 'lp')]
    public function lp(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findAll();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            return $this->render('admin/listperson.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'creation' => 'false',
                'persons' => $p,
                'p' => $person
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }


    //display person 
    #[Route('/dp/{id}', name: 'dp')]
    public function dPerson($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->find($id);
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $form = $this->createForm(PermissionType::class, $p);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($p);
                $em->flush();
            }
            return $this->render('admin/displayPerson.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'creation' => 'false',
                'form' => $form->createView(),
                'person' => $p,
                'p' => $person
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    //list mudancas 
    #[Route('/lm', name: 'lm')]
    public function lm(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $m = $em->getRepository(Mudancas::class)->findAll();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            return $this->render('admin/listMudancas.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'creation' => 'false',
                'mudancas' => $m,
                'p' => $person
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    // cheack mudancas 
    #[Route('/mud/{id}', name: 'mudAdmin')]
    public function mudAdmin(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $m = $em->getRepository(Mudancas::class)->find($id);
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            return $this->render('admin/displayMudancas.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'creation' => 'false',
                'p' => $person,
                'm' => $m

            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }


    #[Route('/listrequest', name: 'listrequest')]
    public function listrequest(ManagerRegistry $doctrine, Request $request)
    {

        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $listrequest = $em->getRepository(Requestper::class)->findAll();
            //dd($listrequest[0]->getPerson()->getName());
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            return $this->render('admin/listrequest.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'creation' => 'false',
                'p' => $person,
                'listrequest' => $listrequest
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    //display person 
    #[Route('/req/{id}', name: 'requestAdmin')]
    public function requestPermission($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Requestper::class)->find($id);
            $person = $p->getPerson();
            $form = $this->createForm(RequestadminType::class, $p);
            $form->handleRequest($request);
            $pers = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($p->getApproves() == 'yes') {
                    $person->setPermission('ler criar atualização');
                    $em->persist($person);
                    $em->persist($p);
                    $em->flush();
                    return $this->redirectToRoute('listrequest');
                } else {
                    $person->setPermission('');
                    $em->persist($person);
                    $em->persist($p);
                    $em->flush();
                    return $this->redirectToRoute('listrequest');
                }
            }
            return $this->render('admin/displayRequest.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'creation' => 'false',
                'form' => $form->createView(),
                'person' => $person,
                'req' => $p,
                'p' => $pers
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/close/mud/{id}', name: 'closemud')]
    public function closeMud(ManagerRegistry $doctrine, Request $request, $id): Response
    {

        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $mudancas = $em->getRepository(Mudancas::class)->find($id);
            $mudancas->setDone('Feito');
            $em->persist($mudancas);
            $em->flush();
            return $this->redirectToRoute('mudAdmin', ['id' => $id]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/open/mud/{id}', name: 'openmud')]
    public function openMud(ManagerRegistry $doctrine, Request $request, $id): Response
    {

        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $mudancas = $em->getRepository(Mudancas::class)->find($id);
            $conn = $doctrine->getConnection();
            $sql = 'UPDATE process SET status ="created" WHERE mudancas_id = :mudancas_id ;';
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
            $ln =  $resultSet->fetchAllAssociative();
            $mudancas->setDone('');
            $em->persist($mudancas);
            $em->flush();
            return $this->redirectToRoute('mudAdmin', ['id' => $id]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/delete/mud/{id}', name: 'deletemud')]
    public function deletemud(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
$session = $request->getSession();
if ($session->get('token_admin') != '') {
    $em = $doctrine->getManager();
    $mudancas = $em->getRepository(Mudancas::class)->find($id);
    $conn = $doctrine->getConnection();
    $mudf = $mudancas->getTypeMud();
    
    // Get all processes related to mudancas
    $sql = 'SELECT * FROM process WHERE mudancas_id = :mudancas_id;';
    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
    $ln = $resultSet->fetchAllAssociative();
    
    // Delete from sector_process table
    foreach ($ln as $key => $value) {
        $sql = 'DELETE FROM sector_process WHERE process_id = :process_id;';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['process_id' => $value['id']]);
    }
    
    // Delete from mudancas_sector table
    $sql = 'DELETE FROM mudancas_sector WHERE mudancas_id = :mudancas_id;';
    $stmt = $conn->prepare($sql);
    $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
    
    // Check if the export_mud table exists
    $tableCheckSql = "SHOW TABLES LIKE 'export_mud';";
    $tableExists = $conn->fetchOne($tableCheckSql);
    
    if ($tableExists) {
        // Delete from export_mud table
        $sql = 'DELETE FROM export_mud WHERE mudanca_id = :process_id;';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['process_id' => $mudancas->getId()]);
    }
    
    // Delete from process table
    $sql = 'DELETE FROM process WHERE mudancas_id = :mudancas_id;';
    $stmt = $conn->prepare($sql);
    $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
    
    // Delete from email table
    $sql = 'DELETE FROM email WHERE mudancas_id = :mudancas_id;';
    $stmt = $conn->prepare($sql);
    $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
    
    // Delete from api_token table
    $sql = 'DELETE FROM api_token WHERE mud_id = :mudancas_id;';
    $stmt = $conn->prepare($sql);
    $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
    
    // Handle mudancasSoft if it exists
    if ($mudancas->getMudS() != null) {
        try {
            $mudancasSoft = $em->getRepository(MudancasSoftware::class)->find($mudancas->getMudS()->getId());
            $mudancasSoft->setReference(null);
            $mudancas->setMudS(null);
            $mudancas->setTypeMud(null);
            $em->remove($mudancasSoft);
            $em->flush();
        } catch (\Throwable $th) {
            // Handle exception if necessary
        }
    }
    
    // Finally, delete from mudancas table
    $sql = 'DELETE FROM mudancas WHERE id = :mudancas_id;';
    $stmt = $conn->prepare($sql);
    $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
    
    return $this->redirectToRoute('export');
} else {
    return $this->redirectToRoute('app_mudancas');
}
    }


    #[Route('/deleteAllqehenoifehfoigvhntrzghiehfaeuifhfheazucohgruheuieahzcuigeaoefc', name: 'deleteAll')]
    public function deleteAllmud(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $mudancass = $em->getRepository(Mudancas::class)->findAll();
            $conn = $doctrine->getConnection();
            foreach ($mudancass as $key => $mudancas) {
                $id = $mudancas->getId();
                # code...
                $mudf = $mudancas->getTypeMud();

                $sql = 'select * FROM process WHERE mudancas_id = :mudancas_id ;';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
                $ln =  $resultSet->fetchAllAssociative();



                foreach ($ln as $key => $value) {
                    $sql = 'Delete FROM sector_process WHERE process_id = :mudancas_id ;';
                    $stmt = $conn->prepare($sql);
                    $resultSet = $stmt->executeQuery(['mudancas_id' => $value['id']]);
                    $ln2 =  $resultSet->fetchAllAssociative();
                }
                $sql = 'Delete FROM mudancas_sector WHERE mudancas_id = :mudancas_id ;';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
                $ln =  $resultSet->fetchAllAssociative();
                $sql = 'Delete FROM process WHERE mudancas_id = :mudancas_id ;';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
                $ln =  $resultSet->fetchAllAssociative();

                /**testet */
                $sql = 'DELETE em
             FROM email em
             WHERE em.mudancas_id = :email;';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(['email' => $mudancas->getId()]);
                $ln =  $resultSet->fetchAllAssociative();

                $sql = 'Delete FROM mudancas_sector WHERE mudancas_id = :mudancas_id ;';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
                $ln =  $resultSet->fetchAllAssociative();


                $sql = 'Delete FROM api_token WHERE mud_id = :mudancas_id ;';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
                $ln =  $resultSet->fetchAllAssociative();

                if ($mudancas->getMudS() != null) {
                    try {
                        //code...
                        $mudancasSoft = $em->getRepository(MudancasSoftware::class)->find($mudancas->getMudS()->getId());
                        $mudancasSoft->setReference(null);
                        $mudancas->setMudS(null);
                        $mudancas->setTypeMud(null);
                        $em->remove($mudancasSoft);
                        $em->flush();
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }

                $sql = 'Delete FROM mudancas WHERE id = :mudancas_id ;';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
                $ln =  $resultSet->fetchAllAssociative();
            }
            return $this->redirectToRoute('export');
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }


    #[Route('/addpersonmanual', name: 'addperson')]
    public function addPerson(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $per =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $person = new Person();
            $form = $this->createForm(AddPersonType::class, $person);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                //strtolower
                //ucwords
                $persName = strtolower($person->getName());
                $persName = ucwords($persName);

                $pers =  $em->getRepository(Person::class)->findOneBy(['name' => $persName]);
                if ($pers == null) {
                    $person->setName($persName);
                    $em->persist($person);
                    $em->flush();
                } else {
                    $pers->setEmail($person->getEmail());
                    $em->flush();
                }
                return $this->redirectToRoute('lp');
            }
            return $this->render('admin/addperson.html.twig', [
                'controller_name' => 'Admin',
                'form' => $form->createView(),
                'type' => 'add',
                'sent' => 'false',
                'p' => $per
            ]);
        } else {
            return $this->redirectToRoute('app_admin');
        }
    }


    #[Route('/editpersonmanual/{id}', name: 'ep')]
    public function EditarPerson($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $per =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $person =  $em->getRepository(Person::class)->find($id);

            //dd($person);

            $form = $this->createForm(EditPersonType::class, $person);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($person);
                $em->flush();
                return $this->redirectToRoute('lp');
            }
            return $this->render('admin/addperson.html.twig', [
                'controller_name' => 'Admin',
                'type' => 'update',
                'form' => $form->createView(),
                'sent' => 'false',
                'p' => $per
            ]);
        } else {
            return $this->redirectToRoute('app_admin');
        }
    }

    #[Route('/delete/{id}', name: 'delperson')]
    public function upPerson(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $person = $em->getRepository(Person::class)->find($id);
            $em->remove($person);
            $em->flush();
            return $this->redirectToRoute('lp');
        } else {
            return $this->redirectToRoute('app_admin');
        }
    }

    /**Login Administrator  */
    #[Route('/login', name: 'app_login_admin')]
    public function login(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        //Check Session
        $session = new Sessions();
        $json = new FunctionUsers();
        $session = $session->checkSessionAdmin($request->getSession());
        if ($session == false) {
            $user = new Person();
            $form = $this->createForm(PersonType::class, $user);
            $form->handleRequest($request);

            $theme = new ThemeFn();
            $theme = $theme->getTheme($em, 'user');
            if ($form->isSubmitted() && $form->isValid()) {
                $username = $form["userName"]->getData();
                $password = $form["password"]->getData();
                $logged = $json->loginAdmin($username, $password, $em, $request);
                if ($logged) {
                    $up = new UsersPermission();
                    if ($up->getPermission($username, $em) != 4) {
                        return $this->redirectToRoute('logout_admin');
                    } else {
                        return $this->redirectToRoute('app_administartor');
                    }
                } else {
                    return $this->render('admin/index.html.twig', [
                        'controller_name' => 'Administartor Avaliação de Fornecedores',
                        'form' => $form->createView(),
                        'password' => 'wrong',
                        'login' => 'true',
                        'password' => null,
                        'theme' => $theme,
                        'form' => $form->createView()
                    ]);
                }
            }

            return $this->render('admin/index.html.twig', [
                'controller_name' => 'Administartor Avaliação de Fornecedores',
                'login' => 'true',
                'password' => null,
                'theme' => $theme,
                'form' => $form->createView()
            ]);
        } else {
            return $this->redirectToRoute('app_administartor');
        }
    }

    #[Route('/cliente', name: 'app_cliente')]
    public function cliente(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $list = $em->getRepository(Client::class)->findAll();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            return $this->render('admin/client.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'list',
                'p' => $person,
                'list' => $list
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/add/cliente/', name: 'add_client')]
    public  function clientAdd(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $sector = new Client();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);

            $form = $this->createForm(AddClientType::class, $sector);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $em->persist($sector);
                $em->flush();
                return $this->redirectToRoute('app_cliente');
            }
            return $this->render('admin/client.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'create',
                'p' => $person,
                'form'  => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }


    #[Route('/edit/cliente/{id}', name: 'edit_client')]
    public  function ClientById($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $sector = $em->getRepository(Client::class)->find($id);

            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $form = $this->createForm(ClientType::class, $sector);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($sector);
                $em->flush();
                return $this->redirectToRoute('app_cliente');
            }
            return $this->render('admin/client.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'update',
                'p' => $person,
                's' => $sector,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/sectors', name: 'app_sectors')]
    public  function sectorsList(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $list = $em->getRepository(Sector::class)->findAll();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            return $this->render('admin/sectors.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'list',
                'p' => $person,
                'list' => $list
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/add/sector', name: 'add_sector')]
    public  function sectorAdd(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {


            $em = $doctrine->getManager();
            /**
             * Check Sector
             * 
             */
            $sectors = file_get_contents('sector.json');
            $sectors = json_decode($sectors);
            foreach ($sectors as $key => $sector) {
                $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $sector[0]]);
                if ($sec == null) {
                    $sec = new Sector();
                    $manager =  $em->getRepository(Person::class)->findOneBy(['email' => $sector[2]]);
                    $coordinator =  $em->getRepository(Person::class)->findOneBy(['email' => $sector[1]]);
                    $sec->setName($sector[0]);
                    $sec->setManager($manager);
                    $sec->setCoordinator($coordinator);
                    $em->persist($sec);
                    $em->flush();
                }
            }
            $sector = new Sector();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);

            $form = $this->createForm(SectorType::class, $sector);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $em->persist($sector);
                $em->flush();
                return $this->redirectToRoute('app_sectors');
            }
            return $this->render('admin/sectors.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'create',
                'p' => $person,
                'form'  => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }



    #[Route('/delete/sector/{id}', name: 'delete_sector')]
    public  function deletesectorById($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        //$request->header_remove();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $sector = $em->getRepository(Sector::class)->find($id);
            $em->remove($sector);
            $em->flush();
            return $this->redirectToRoute('app_sectors');
        } else {
            return $this->redirectToRoute('app_admin');
        }
    }

    #[Route('/edit/projevisa', name: 'edit_projevisa')]
    public  function projevisaById( ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $projevisa = $em->getRepository(Projevisa::class)->find(1);
            if($projevisa == null) {
                $projevisa = new Projevisa();
                $em->persist($projevisa);
                $em->flush();
            } 
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);

            $form = $this->createForm(ProjevisaType::class, $projevisa);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid() ) {
                $em->persist($projevisa);
                $em->flush();
                return $this->redirectToRoute('app_sectors');
            }
            return $this->render('admin/projevisa.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'update',
                'p' => $person,
                's' => $projevisa,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/edit/sector/{id}', name: 'edit_sector')]
    public  function sectorById($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $sector = $em->getRepository(Sector::class)->find($id);
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);

            $oldCorrdinator = $sector->getCoordinator();
            $oldManager = $sector->getManager();

            $form = $this->createForm(SectorType::class, $sector);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($sector->getCoordinator() != $oldCorrdinator) {
                    $mudancas = $em->getRepository(Mudancas::class)->findAll();
                    foreach ($mudancas as $key => $mud) {
                        if ($mud->getDone() == null) {
                            $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $mud]);
                            $sectorProcess = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);

                            foreach ($sectorProcess as $key => $sp) {
                                if ($sp->getSector() == $sector) {
                                    if ($sp->getComment() == null & $sp->getAppSectorMan() == null) {
                                        $sp->setPerson($sector->getCoordinator());
                                    }
                                }
                            }
                        }
                    }
                }

                if ($sector->getManager() != $oldManager) {
                    $mudancas = $em->getRepository(Mudancas::class)->findAll();
                    foreach ($mudancas as $key => $mud) {
                        if ($mud->getDone() == null) {
                            if ($mud->getManagerUserApp() == null) {
                                $mud->setManagerUserAdd($sector->getManager());
                            }
                        }
                    }
                }

                $em->persist($sector);
                $em->flush();
                return $this->redirectToRoute('app_sectors');
            }
            return $this->render('admin/sectors.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'update',
                'p' => $person,
                's' => $sector,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    public  function sectorById2($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $sector = $em->getRepository(Sector::class)->find($id);
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $form = $this->createForm(SectorType::class, $sector);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $em->persist($sector);
                $em->flush();
                return $this->redirectToRoute('app_sectors');
            }
            return $this->render('admin/sectors.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'update',
                'p' => $person,
                's' => $sector,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/email', name: 'email')]
    public function emaill(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $email = $doctrine->getManager()->getRepository(ConfigEmail::class)->find(1);

            if ($email == null) {
                $email = new ConfigEmail();
                $email->setHost('smtp.office365.com');
                $email->setSmtpAuth(true);
                $email->setPort(587);
                $email->setUsername('noreply@serdia.com.br');
                $email->setPassword('9BhAsZw8a8ZrnQzX');
                $email->setEmailSystem('noreply@serdia.com.br');
                $email->setTitleObj('Serdia Control Mudanças');
                $email->setSubject('Controle de Mudanças *');
                $email->setChartSet('UTF-8');
                $em->persist($email);
                $em->flush();
            }


            $form = $this->createForm(ConfigemailType::class, $email);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                return $this->redirectToRoute('email');
            }
            return $this->render('admin/email.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'p' => $person,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }

    #[Route('/emails', name: 'emails')]
    public function emails(ManagerRegistry $doctrine, Request $request)
    {
        try {
            //code...
            $session = new Session();
            $session = $request->getSession();

            if ($session->get('token_admin') != '') {
                $em = $doctrine->getManager();
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
                $emails = $doctrine->getManager()->getRepository(EmailToSendConfig::class)->findAll();

                /*$form = $this->createForm(emailType::class, $emails);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                return $this->redirectToRoute('email');
            }*/
                return $this->render('admin/emailsList.html.twig', [
                    'controller_name' => 'Atualizar Mudancas',
                    'p' => $person,
                    'emails' => $emails,
                    'type' => 'list'
                ]);
            } else {
                return $this->redirectToRoute('app_mudancas');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    #[Route('/AddEmails', name: 'AddEmails')]
    public function AddEmails(ManagerRegistry $doctrine, Request $request)
    {
        try {
            //code...
            $session = new Session();
            $session = $request->getSession();

            if ($session->get('token_admin') != '') {
                $em = $doctrine->getManager();
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
                //$emails = $doctrine->getManager()->getRepository(EmailToSendConfig::class)->findAll();
                $email = new EmailToSendConfig();
                $form = $this->createForm(EmailToSendConfigType::class, $email);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($email);
                    $em->flush();
                    return $this->redirectToRoute('emails');
                }
                return $this->render('admin/emailsList.html.twig', [
                    'controller_name' => 'Atualizar Mudancas',
                    'p' => $person,
                    'email' => $email,
                    'form' => $form,
                    'type' => 'create'
                ]);
            } else {
                return $this->redirectToRoute('app_mudancas');
            }
        } catch (\Throwable $th) {
        }
    }


    #[Route('/UpdateEmails/{id}', name: 'UpdateEmails')]
    public function UpdateEmails(ManagerRegistry $doctrine, $id, Request $request)
    {
        try {
            //code...
            $session = new Session();
            $session = $request->getSession();

            if ($session->get('token_admin') != '') {
                $em = $doctrine->getManager();
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
                $email = $doctrine->getManager()->getRepository(EmailToSendConfig::class)->find($id);
                $form = $this->createForm(EmailToSendConfigType::class, $email);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($email);
                    $em->flush();
                    return $this->redirectToRoute('emails');
                }
                return $this->render('admin/emailsList.html.twig', [
                    'controller_name' => 'Atualizar Mudancas',
                    'p' => $person,
                    'email' => $email,
                    'form' => $form,
                    'type' => 'create'
                ]);
            } else {
                return $this->redirectToRoute('app_mudancas');
            }
        } catch (\Throwable $th) {
        }
    }

    #[Route('/email/{id}', name: 'emailAdmin')]
    public function emailSendToClient(ManagerRegistry $doctrine, Request $request, $id)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);

            $this->sendEmail($doctrine, $request, $mud->getClient(), $mud, $person, 'client', false, $mud->getClient());
            return $this->redirectToRoute('mudAdmin', ['id' => $id]);
        } else {
            return $this->redirectToRoute('app_mudancas');
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

    #[Route('/export', name: 'export')]
    public function getExportData(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();

        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();

            $mudIds = $request->request->all();
            if ($mudIds != null) {
                /*
                array:8 [▼
                     "status" => "Solicitação Reprovada"
                     "dateInicio" => "2023-10-06"
                     "dateTermino" => "2023-10-23"
                     "tipo" => "Solicitante"
                     "area" => "017 – GARANTIA DA QUALIDADE MATRIZ (CALIBRAÇÃO)"
                     "client" => ""
                     "person" => "João Ramiro"
                     "dateApp" => "2023-10-23"
                ] */



                $emptyOrNullKeys = [];

                $parameters = [];
                $whereConditions = [];
                foreach ($mudIds as $key => $value) {
                    if (empty($value) || $value === "null") {
                        $emptyOrNullKeys[] = $key;
                    }



                    // Assume $sectorId and $processId are variables you're working with.

                    if (($value != "null" || $value != "") && ($key === 'status')) {

                        /**Status search 
                         * 
                         * Solicitação Aprovada
                         * Solicitação Reprovada
                         * Mudança Aprovada
                         * Mudança Reprovada
                         * Mudança Aceita
                         * Mudança Rejeitada
                         * Mudança implementada
                         * Mudança não implementada  implementadas e fechadas
                         */
                        if ($value === 'Solicitação Aprovada') {
                            $whereConditions[] = "mud.manager_user_app = ?";
                            $parameters[] = 1;

                            $whereConditions[] = "mud.app_man = ?";
                            $parameters[] = null;
                        } elseif ($value === 'Solicitação Reprovada') {
                            $whereConditions[] = "mud.manager_user_app = ?";
                            $parameters[] = 2;

                            $whereConditions[] = "mud.app_man = ?";
                            $parameters[] = null;
                        } elseif ($value === 'Mudança Aceita') {
                            $whereConditions[] = "mud.app_man = ?";
                            $parameters[] = 1;

                            $whereConditions[] = "mud.app_gest = ?";
                            $parameters[] = null;
                        } elseif ($value === 'Mudança Rejeitada') {
                            $whereConditions[] = "mud.app_man = ?";
                            $parameters[] = 2;
                        } elseif ($value === 'Mudança Aprovada') {
                            $whereConditions[] = "mud.app_gest = ?";
                            $parameters[] = 1;
                        } elseif ($value === 'Mudança Reprovada') {
                            $whereConditions[] = "mud.app_gest = ?";
                            $parameters[] = 2;
                        } elseif ($value === 'Mudança implementada') {
                            $whereConditions[] = "mud.implemented = ?";
                            $parameters[] = 1;
                        } elseif ($value === 'Mudança não implementada  implementadas e fechadas') {
                            $whereConditions[] = "mud.manager_user_app = ?";
                            $parameters[] = 2;
                        }
                    } elseif (($value != "null" || $value != "") && ($key === 'dateInicio')) {
                        //dateInicio search
                        $whereConditions[] = "sp.sector_id = ?";
                        $parameters[] = $value;
                    } elseif (($value != "null" || $value != "") && ($key === 'dateTermino')) {
                        //dateTermino search 
                        $whereConditions[] = "sp.sector_id = ?";
                        $parameters[] = $value;
                    } elseif (($value != "null" || $value != "") && ($key === 'tipo')) {
                        ///tipo search 
                        $whereConditions[] = "sp.sector_id = ?";
                        $parameters[] = $value;
                    } elseif (($value != "null" || $value != "") && ($key === 'area')) {
                        //area search
                        $whereConditions[] = "sp.sector_id = ?";
                        $parameters[] = $value;
                    } elseif (($value != "null" || $value != "") && ($key === 'client')) {
                        //client search 
                        $whereConditions[] = "sp.sector_id = ?";
                        $parameters[] = $value;
                    } elseif (($value != "null" || $value != "") && ($key === 'person')) {
                        //person search 
                        $whereConditions[] = "sp.sector_id = ?";
                        $parameters[] = $value;
                    } elseif (($value != "null" || $value != "") && ($key === 'dateApp')) {
                        //dateApp search 
                        $whereConditions[] = "sp.sector_id = ?";
                        $parameters[] = $value;
                    }
                }
                // ... Add similar conditions for other parameters as needed ...

                // Always include conditions that don't depend on parameters.
                $whereConditions[] = "p.mudancas_id = mud.id";
                $whereConditions[] = "sp.process_id = p.id";

                // Now, build the full SQL string.
                $em->clear();
                $sql = 'SELECT mud.id FROM sector_process as sp, mudancas as mud, process as p WHERE ' . implode(' AND ', $whereConditions);

                $conn = $doctrine->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute($parameters);
                $resultSet = $stmt->executeQuery();
                // get the id of the Porcess
                // dd($process->getId());

                $dm =  $resultSet->fetchAllAssociative();
                dd($dm);
            } else {


                $sectors = $em->getRepository(Sector::class)->findAll();
                $list = $em->getRepository(Mudancas::class)->findAll();
                $client = $em->getRepository(Client::class)->findAll();
                $managers = $em->getRepository(Person::class)->findAll();
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            }
            return $this->render('admin/listMudancas.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'list',
                'p' => $person,
                'client' => $client,
                'managers' => $managers,
                'sectors' => $sectors,
                'mudancas' => $list,
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }


    //http://10.100.1.180/controleMudancas/public/adminmud/exportResult

    #[Route('/exportResult', name: 'exportResult')]
    public function getExportResultData(ManagerRegistry $doctrine, Request $request)
    {
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();

            // Fetch all required Mudancas records in a single query
            $mudIds = $request->request->all();
            $mud = $em->getRepository(Mudancas::class)->findBy(['id' => $mudIds]);

            $process = [];
            foreach ($mud as $value) {
                $process[] = $em->getRepository(Process::class)->findOneBy(['mudancas' => $value]);
            }

            $secProcess = [];
            foreach ($process as $value) {
                $secProcess[] = $em->getRepository(SectorProcess::class)->findOneBy(['process' => $value]);
            }

            $spreadsheet = (new Excel())->generateExcel($mud, $doctrine);
            $writer = new Xlsx($spreadsheet);
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath =  $publicDirectory . '/public/Admin.xlsx';
            $writer->save($excelFilepath);
            return $this->redirectToRoute('exportFile');
        }
    }
}




            /*if (($request->request->get('status') == "null" &
                    $request->request->get('dateInicio') == "" &
                    $request->request->get('dateTermino') == "" &
                    $request->request->get('tipo') == "null" &
                    $request->request->get('area') == "null" &
                    $request->request->get('client') == "null" &
                    $request->request->get('person') == "null" &
                    $request->request->get('dateApp') == "") || sizeof($request->request) == 0
            ) {
                $list = $em->getRepository(Mudancas::class)->findAll();
                $sectors = $em->getRepository(Sector::class)->findAll();
                $client = $em->getRepository(Client::class)->findAll();
                $managers = $em->getRepository(Person::class)->findAll();
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            } else {

                $list = $em->getRepository(Mudancas::class)->findAll();

/*
                if ($request->request->get('status') == "Mudança Rejeitada") {
                    $listA = [];
                    foreach ($list as $key => $value) {
                        if ($value->getImplemented() == "2" &&  $value->getManagerUserApp() == "1" && $value->getAppMan() == "2" && $value->getAppGest() == null) {
                            array_push($listA, $value);
                        }
                    }
                    $list = $listA;
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        foreach ($list as $key => $value) {
                            if (
                                $value->getAddBy()->getName() == $request->request->get('person')
                            ) {
                                array_push($listA, $value);
                            }
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } elseif ($request->request->get('status') == "Mudança implementada") {
                    $listA = [];

                    foreach ($list as $key => $value) {
                        if ($value->getImplemented() == "1") {
                            array_push($listA, $value);
                        }
                    }

                    $list = $listA;

                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } elseif ($request->request->get('status') == "Mudança não implementada  implementadas e fechadas") {
                    $listA = [];
                    foreach ($list as $key => $value) {
                        if ($value->getManagerUserApp() == "1" && $value->getAppMan() == "1" && $value->getAppGest() == "1" && $value->getImplemented() == "2") {
                            array_push($listA, $value);
                        }
                    }
                    $list = $listA;
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } elseif ($request->request->get('status') == "Solicitação Aprovada") {
                    $listA = [];
                    foreach ($list as $key => $value) {
                        if ($value->getImplemented() == null && $value->getManagerUserApp() == "1" && $value->getAppMan() == null && $value->getAppGest() == null) {
                            array_push($listA, $value);
                        }
                    }
                    $list = $listA;
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } elseif ($request->request->get('status') == "Solicitação Reprovada") {
                    $listA = [];
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } elseif ($request->request->get('status') == "Mudança Aprovada") {
                    $listA = [];

                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            foreach ($list as $key => $value) {
                                if ($value->getImplemented() == null && $value->getManagerUserApp() == "1" && $value->getAppMan() == "1" && $value->getAppGest() == null) {
                                    array_push($listA, $value);
                                }
                            }
                        }
                    }
                    $list = $listA;
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } elseif ($request->request->get('status') == "Mudança Reprovada") {
                    $listA = [];
                    foreach ($list as $key => $value) {
                        if ($value->getImplemented() == null && $value->getManagerUserApp() == "1" && $value->getAppMan() == "1" && $value->getAppGest() == "2") {
                            array_push($listA, $value);
                        }
                    }
                    $list = $listA;
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } elseif ($request->request->get('status') == "Mudança Aceita") {
                    $listA = [];

                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            foreach ($list as $key => $value) {
                                if ($value->getImplemented() == null && $value->getManagerUserApp() == "1" && $value->getAppMan() == "1" && $value->getAppGest() == null) {
                                    array_push($listA, $value);
                                }
                            }
                        }
                    }
                    $list = $listA;
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                } else {
                    $listA = [];
                    if ($request->request->get('tipo') == "Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Solicitante") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAddBy()->getFunction()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAddBy()->getFunction()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gerente Aprovação") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getManager()->getName() == $request->request->get('person') &&
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if (
                                        $value->getAreaResp()->getName() == $request->request->get('area')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {
                                if (
                                    $value->getAreaResp()->getManager()->getName() == $request->request->get('person')
                                ) {
                                    array_push($listA, $value);
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Gestor da Mudança") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            if ($request->request->get('person') != "null") {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...
                                        if (
                                            $value->getMangerMudancas()->getName() == $request->request->get('person') &&
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            } else {
                                foreach ($list as $key => $value) {
                                    if ($value->getMangerMudancas() != null) {
                                        # code...

                                        if (
                                            $value->getMangerMudancas()->getFunction()->getName() == $request->request->get('area')
                                        ) {
                                            array_push($listA, $value);
                                        }
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            foreach ($list as $key => $value) {

                                if ($value->getMangerMudancas() != null) {
                                    if (
                                        $value->getMangerMudancas()->getName() == $request->request->get('person')
                                    ) {
                                        array_push($listA, $value);
                                    }
                                }
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    } elseif ($request->request->get('tipo') == "Área Impactada") {
                        $listA = [];
                        if ($request->request->get('area') != "null") {
                            $proc = [];
                            $sec = $em->getRepository(Sector::class)->findOneBy(['name' => $request->request->get('area')]);
                            $procSec = $em->getRepository(SectorProcess::class)->findBy(['Sector' => $sec]);
                            if ($request->request->get('person') != "null") {
                                $process = [];
                                foreach ($procSec as $key => $value) {
                                    if ($value->getPerson()->getName() == $request->request->get('person')) {
                                        array_push($proc, $value);
                                    }
                                }

                                foreach ($proc as $key => $val) {
                                    $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                    array_push($process, $proc);
                                }

                                foreach ($process as $key => $v) {
                                    array_push($listA, $v->getMudancas());
                                }
                            } else {
                                foreach ($procSec as $key => $value) {
                                    $proc = $em->getRepository(Process::class)->find($value->getProcess()->getId());
                                    array_push($process, $proc);

                                    foreach ($process as $key => $v) {
                                        array_push($listA, $v->getMudancas());
                                    }
                                }
                            }
                            $list = $listA;
                        } elseif ($request->request->get('person') != "null") {
                            $procSec = $em->getRepository(SectorProcess::class)->findAll();
                            foreach ($procSec as $key => $value) {
                                if ($procSec->getPerson()->getName() == $request->request->get('person')) {
                                    array_push($proc, $value);
                                }
                            }

                            foreach ($procSec as $key => $val) {
                                $proc = $em->getRepository(Process::class)->find($val->getProcess()->getId());
                                array_push($process, $proc);
                            }

                            foreach ($process as $key => $v) {
                                array_push($listA, $v->getMudancas());
                            }
                        } else {
                            $list = $em->getRepository(Mudancas::class)->findAll();
                        }
                    }
                }
            }*/