<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Client;
use App\Entity\ConfigEmail;
use App\Entity\Departemant;
use App\Entity\DepartemantMudancass;
use App\Entity\Email;
use App\Entity\ExportMud;
use App\Entity\Manager;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Requestper;
use App\Entity\Sector;
use App\Form\AddClientType;
use App\Form\AddPersonType;
use App\Form\ClientType;
use App\Form\ConfigemailType;
use App\Form\EditPersonType;
use App\Form\EmailType;
use App\Form\ManagerType;
use App\Form\PermissionType;
use App\Form\PersonType;
use App\Form\RequestadminType;
use App\Form\RequestperType;
use App\Form\SectorType;
use App\Entity\Process;
use App\Entity\SectorProcess;
use App\Model\Class\Excel;
use App\Model\Class\Excel2;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                    $m = $em->getRepository(Manager::class)->findAll();
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
                    $val = intval($this->presentNotDone($val, $val2));
                    $size = sizeof($mudancas);
                    return $this->render('admin/dash.html.twig', [
                        'controller_name' => 'AdminController',
                        'percent' => $val,
                        'size' => $size,
                        'mud' => $arr,
                        'm' => $m,
                        'p' => $p,
                        'mudancas' => $mudancas
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

    //add manager
    #[Route('/am', name: 'managers')]
    public function amanagers(ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            if ($p->getRole() == null) {
                return $this->redirectToRoute('app_mudancas');
            } else {
                $manager = new Manager();
                $m = $em->getRepository(Manager::class)->findAll();
                $form = $this->createForm(ManagerType::class, $manager);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $persons = $form["persons"]->getData();
                    foreach ($persons as $person) {
                        $manager = $em->getRepository(Manager::class)->findOneBy(['person' => $person]);
                        $person->setPermission('tudo');
                        if ($manager == null) {
                            $manager = new Manager();
                            $manager->setPerson($person);
                            $em->persist($manager);
                            $em->flush();
                        }
                        $em->persist($person);
                        $em->flush();
                    }
                    return $this->redirectToRoute('managers');
                }
                return $this->render('admin/manager.html.twig', [
                    'controller_name' => 'Atualizar Mudancas',
                    'login' => 'null',
                    'creation' => 'false',
                    'managers' => $m,
                    'p' => $p,
                    'form' => $form->createView(),
                ]);
            }
        } else {

            return $this->redirectToRoute('app_mudancas');
        }
    }

    //delete manager 
    #[Route('/dm/{id}', name: 'dm')]
    public function dmanagers($id, ManagerRegistry $doctrine, Request $request)
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Manager::class)->find($id);
            $em->remove($p);
            $em->flush();
            return $this->redirectToRoute('managers');
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
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

            //dd($m);
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

        //$request->header_remove();
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

        //$request->header_remove();
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

        //$request->header_remove();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $mudancas = $em->getRepository(Mudancas::class)->find($id);
            $conn = $doctrine->getConnection();
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

            $sql = 'Delete FROM mudancas WHERE id = :mudancas_id ;';
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
            $ln =  $resultSet->fetchAllAssociative();

            return $this->redirectToRoute('lm');
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
            $form = $this->createForm(LoginFormType::class, $user);
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

    #[Route('/edit/sector/{id}', name: 'edit_sector')]
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
            $empty = true;
            $listcheckStat = [];
            foreach ($mudIds as $key => $value) {
                if ($value != "null") {
                    if ($value != "") {
                        $empty = false;
                    }
                }
            }
            if ($empty == true) {
                $mudIds = null;
            }
            if ($mudIds != null) {
                $list = [];
                $listDate = [];
                $listDateInit = [];
                $listDateApp = [];
                $listDateTypePers = [];
                $listMudbyArea = [];
                $listClient = [];
                $export = $em->getRepository(ExportMud::class)->findAll();
                // Assume $sectorId and $processId are variables you're working with.  
                for ($i = 0; $i < sizeof($export); $i++) {
                    $valueExp = $export[$i];
                    $type = null;
                    $coordinator = null;
                    dd($mudIds); 
                    foreach ($mudIds as $key => $value) {
                        if (($value != "null" || $value != "") && ($key == 'status')) {

                            if ($value === 'Solicitação Aprovada') {
                                //
                                if (($valueExp->getMudanca()->getManagerUserApp() == 1) &&
                                    $valueExp->getMudanca()->getAppMan() == null &&
                                    $valueExp->getMudanca()->getImplemented() == null
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } elseif (($valueExp->getMudanca()->getNansenNumber() != null) &&
                                    $valueExp->getMudanca()->getAppMan() == null &&
                                    $valueExp->getMudanca()->getImplemented() == null
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                                //var_dump($listcheckStat);
                            } elseif ($value === 'Solicitação Reprovada') {
                                if (($valueExp->getMudanca()->getManagerUserApp() == 2) &&
                                    $valueExp->getMudanca()->getAppMan() == null &&
                                    $valueExp->getMudanca()->getImplemented() != null
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                            } elseif ($value === 'Mudança Aceita') {
                                if (($valueExp->getMudanca()->getAppGest() == 1) &&
                                    $valueExp->getMudanca()->getImplemented() == null
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                            } elseif ($value === 'Mudança Rejeitada') {
                                if (($valueExp->getMudanca()->getAppGest() == 2) &&
                                    $valueExp->getMudanca()->getImplemented() != null
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    $isDone = null;
                                    foreach ($valueExp->getSectorProcess() as $key => $value) {
                                        if ($value->getAppSectorMan() == 2) {
                                            $isDone = 1;
                                        }
                                    }
                                    if ($isDone === 1) {
                                        array_push($list, $valueExp->getMudanca());
                                    } else {
                                        array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                    }
                                }
                            } elseif ($value === 'Mudança Aprovada') {
                                if (($valueExp->getMudanca()->getAppMan() == 1) &&
                                    $valueExp->getMudanca()->getAppGest() == null &&
                                    $valueExp->getMudanca()->getDone() != 'Feito' &&
                                    $valueExp->getMudanca()->getImplemented() == null
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                            } elseif ($value === 'Mudança Reprovada') {
                                if (($valueExp->getMudanca()->getAppMan() == 2) &&
                                    $valueExp->getMudanca()->getAppGest() == null
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                            } elseif ($value === 'Mudança implementada') {
                                if (($valueExp->getMudanca()->getAppMan() == 1) &&
                                    $valueExp->getMudanca()->getAppGest() == 1 &&
                                    $valueExp->getMudanca()->getImplemented() == 1
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                            } elseif ($value === 'Mudança não implementada  implementadas e fechadas') {
                                if (($valueExp->getMudanca()->getAppMan() == 1) &&
                                    $valueExp->getMudanca()->getAppGest() == 1 &&
                                    $valueExp->getMudanca()->getImplemented() == 2
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                            } elseif ($value === 'Mudança Aberta') {
                                if (
                                    $valueExp->getMudanca()->getManagerUserApp() == null && $valueExp->getMudanca()->getImplemented() != 2
                                ) {
                                    array_push($list, $valueExp->getMudanca());
                                } else {
                                    array_push($listcheckStat, $valueExp->getMudanca()->getId());
                                }
                            } else {
                                array_push($list, $valueExp->getMudanca());
                            }
                        }/* elseif (($value != "null" || $value != "") && ($key === 'dateInicio')) {
                                try {
                                    $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                    $date2 = DateTime::createFromFormat('d-m-Y', $valueExp->getMudanca()->getStartMudancas());
                                    $date1 = $date1->format('F j, Y');
                                    $date2 = $date2->format('F j, Y');
                                    if ($date1 >= $date2) {
                                        array_push($listDateInit, $valueExp->getMudanca());
                                    }
                                } catch (\Throwable $th) {
                                }
                        }elseif (($value != "null" || $value != "") && ($key === 'dateInicioAte')) {
                                try {
                                    $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                    $date2 = DateTime::createFromFormat('d-m-Y', $valueExp->getMudanca()->getStartMudancas());
                                    $date1 = $date1->format('F j, Y');
                                    $date2 = $date2->format('F j, Y');
                                    if ($date1 <= $date2) {
                                        array_push($listDateInit, $valueExp->getMudanca());
                                    }
                                } catch (\Throwable $th) {
                                }
                        } elseif (($value != "null" || $value != "") && ($key === 'dateTermino')) {
                            //var_dump($value .' '.$valueExp->getMudanca()->getEndMudancas());
                            $date = 0;
                            try {
                                $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                $date2 = DateTime::createFromFormat('d-m-Y', $valueExp->getMudanca()->getEndMudancas());
                                $date1 = $date1->format('F j, Y');
                                $date2 = $date2->format('F j, Y');
                                if ($date1 == $date2) {
                                    array_push($listDate, $valueExp->getMudanca());
                                }
                            } catch (\Throwable $th) {
                            }
                        }*/ elseif (($value != "null" || $value != "") && ($key === 'tipo')) {
                            if ($value === 'Solicitante') {
                                $type = $value;
                            } elseif ($value === 'Gerente Solicitante') {
                                $type = $value;
                            } elseif ($value === 'Gerente Aprovação') {
                                $type = $value;
                            } elseif ($value === 'Gestor da Mudança') {
                                $type = $value;
                            } elseif ($value === 'Área Impactada') {
                                $type = $value;
                            }
                        } elseif (($value != "null" || $value != "") && ($key === 'client')) {
                            if ($valueExp->getMudanca()->getClient() != null) {
                                if ($value == $valueExp->getMudanca()->getClient()->getName()) {
                                    array_push($listClient, $valueExp->getMudanca());
                                }
                            }
                        } elseif (($value != "null" || $value != "") && ($key === 'person')) {
                            if ($type == "Solicitante") {

                                if ($valueExp->getMudanca()->getAddBy() != null) {
                                    if ($value == $valueExp->getMudanca()->getAddBy()->getName()) {
                                        array_push($listDateTypePers, $valueExp->getMudanca());
                                    }
                                }
                            } elseif ($type == "Gerente Solicitante") {

                                if ($valueExp->getMudanca()->getManagerUserAdd() != null) {
                                    if ($value == $valueExp->getMudanca()->getManagerUserAdd()->getName()) {
                                        array_push($listDateTypePers, $valueExp->getMudanca());
                                    }
                                }
                            } elseif ($type == "Gerente Aprovação") {

                                if ($valueExp->getMudanca()->getAreaResp()->getManager() != null) {
                                    if ($value == $valueExp->getMudanca()->getAreaResp()->getManager()->getName()) {
                                        array_push($listDateTypePers, $valueExp->getMudanca());
                                    }
                                }
                            } elseif ($type == "Gestor da Mudança") {
                                if ($valueExp->getMudanca()->getMangerMudancas() != null) {
                                    if ($value == $valueExp->getMudanca()->getMangerMudancas()->getName()) {
                                        array_push($listDateTypePers, $valueExp->getMudanca());
                                    }
                                }
                            } elseif ($type == "Área Impactada") {
                                foreach ($valueExp->getMudanca()->getAreaImpact() as $key => $areaI) {
                                    if ($areaI->getCoordinator() != null) {
                                        $coordinator = $value;
                                        if ($coordinator == $areaI->getCoordinator()->getName()) {
                                            if (sizeof($listDateTypePers) == 0) {
                                                array_push($listDateTypePers, $valueExp->getMudanca());
                                            } else {
                                                if ($listDateTypePers[sizeof($listDateTypePers) - 1] != $valueExp->getMudanca()) {
                                                    array_push($listDateTypePers, $valueExp->getMudanca());
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($valueExp->getMudanca()->getAddBy() != null) {
                                    if ($value == $valueExp->getMudanca()->getAddBy()->getName()) {
                                        if (sizeof($listDateTypePers) == 0) {
                                            array_push($listDateTypePers, $valueExp->getMudanca());
                                        } else {
                                            if ($listDateTypePers[sizeof($listDateTypePers) - 1] != $valueExp->getMudanca()) {
                                                array_push($listDateTypePers, $valueExp->getMudanca());
                                            }
                                        }
                                    }
                                }
                                if ($valueExp->getMudanca()->getManagerUserAdd() != null) {
                                    if ($value == $valueExp->getMudanca()->getManagerUserAdd()->getName()) {
                                        if (sizeof($listDateTypePers) == 0) {
                                            array_push($listDateTypePers, $valueExp->getMudanca());
                                        } else {
                                            if ($listDateTypePers[sizeof($listDateTypePers) - 1] != $valueExp->getMudanca()) {
                                                array_push($listDateTypePers, $valueExp->getMudanca());
                                            }
                                        }
                                    }
                                }
                                if ($valueExp->getMudanca()->getAreaResp()->getManager() != null) {
                                    if ($value == $valueExp->getMudanca()->getAreaResp()->getManager()->getName()) {
                                        if (sizeof($listDateTypePers) == 0) {
                                            array_push($listDateTypePers, $valueExp->getMudanca());
                                        } else {
                                            if ($listDateTypePers[sizeof($listDateTypePers) - 1] != $valueExp->getMudanca()) {
                                                array_push($listDateTypePers, $valueExp->getMudanca());
                                            }
                                        }
                                    }
                                }
                                if ($valueExp->getMudanca()->getMangerMudancas() != null) {
                                    if ($value == $valueExp->getMudanca()->getMangerMudancas()->getName()) {
                                        if (sizeof($listDateTypePers) == 0) {
                                            array_push($listDateTypePers, $valueExp->getMudanca());
                                        } else {
                                            if ($listDateTypePers[sizeof($listDateTypePers) - 1] != $valueExp->getMudanca()) {
                                                array_push($listDateTypePers, $valueExp->getMudanca());
                                            }
                                        }
                                    }
                                }
                                foreach ($valueExp->getMudanca()->getAreaImpact() as $key => $areaI) {
                                    if ($areaI->getCoordinator() != null) {
                                        $coordinator = $value;
                                        if ($coordinator == $areaI->getCoordinator()->getName()) {
                                            if (sizeof($listDateTypePers) == 0) {
                                                array_push($listDateTypePers, $valueExp->getMudanca());
                                            } else {
                                                if ($listDateTypePers[sizeof($listDateTypePers) - 1] != $valueExp->getMudanca()) {
                                                    array_push($listDateTypePers, $valueExp->getMudanca());
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($value != "" && $key === 'dateApp') {
                            if ($type === 'Solicitante') {
                                try {
                                    $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                    $date2 = DateTime::createFromFormat('d-m-Y', $valueExp->getMudanca()->getDataCreation());
                                    $date1 = $date1->format('F j, Y');
                                    $date2 = $date2->format('F j, Y');
                                    if ($date1 == $date2) {
                                        array_push($listDateApp, $valueExp->getMudanca());
                                    }
                                } catch (\Throwable $th) {
                                }
                            } elseif ($type === 'Gerente Solicitante') {
                                try {
                                    $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                    $date2 = DateTime::createFromFormat('d-m-Y', $valueExp->getMudanca()->getDateMUA());
                                    $date1 = $date1->format('F j, Y');
                                    $date2 = $date2->format('F j, Y');
                                    if ($date1 == $date2) {
                                        array_push($listDateApp, $valueExp->getMudanca());
                                    }
                                } catch (\Throwable $th) {
                                }
                            } elseif ($type === 'Gerente Aprovação') {
                                try {
                                    $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                    $date2 = DateTime::createFromFormat('d-m-Y', $valueExp->getMudanca()->getDateAM());
                                    $date1 = $date1->format('F j, Y');
                                    $date2 = $date2->format('F j, Y');
                                    if ($date1 == $date2) {
                                        array_push($listDateApp, $valueExp->getMudanca());
                                    }
                                } catch (\Throwable $th) {
                                }
                            } elseif ($type === 'Gestor da Mudança') {
                                try {
                                    $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                    $date2 = DateTime::createFromFormat('d-m-Y', $valueExp->getMudanca()->getDateAG());
                                    $date1 = $date1->format('F j, Y');
                                    $date2 = $date2->format('F j, Y');
                                    if ($date1 == $date2) {
                                        array_push($listDateApp, $valueExp->getMudanca());
                                    }
                                } catch (\Throwable $th) {
                                }
                            } elseif ($type === 'Área Impactada') {
                                //fetech process  /*
                                foreach ($valueExp->getSectorProcess() as $key => $sp) {
                                    if ($coordinator != null) {
                                        if ($sp->getPerson()) {
                                            if ($sp->getPerson()->getName() == $coordinator) {
                                                $datestr = strval($sp->getDataCreation());
                                                try {
                                                    $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                                    $date2 = date('Y-m-d', strtotime($sp->getDataCreation()));
                                                    $date1 = $date1->format('Y-m-d');
                                                    if ($date1 == $date2) {
                                                        array_push($listDateApp, $valueExp->getMudanca());
                                                    }
                                                } catch (\Throwable $th) {
                                                    dd($th);
                                                }
                                            }
                                        }
                                    } else {
                                        try {
                                            $date1 = DateTime::createFromFormat('Y-m-d', $value);
                                            $date2 = DateTime::createFromFormat('d-m-Y', $sp->getDataCreation());
                                            $date1 = $date1->format('F j, Y');
                                            $date2 = $date2->format('F j, Y');
                                            if ($date1 == $date2) {
                                                array_push($listDateApp, $valueExp->getMudanca());
                                            }
                                        } catch (\Throwable $th) {
                                        }
                                    }
                                }
                            }
                        } elseif ($value != "" && $key === 'area') {
                            if ($type == "Solicitante" || $type == "Gerente Solicitante") {
                                if ($valueExp->getMudanca()->getAddBy() != null) {
                                    try {
                                        if ($valueExp->getMudanca()->getAddBy()->getFunction() != null) {
                                            if ($value == $valueExp->getMudanca()->getAddBy()->getFunction()->getName()) {
                                                array_push($listMudbyArea, $valueExp->getMudanca());
                                            }
                                        }
                                    } catch (\Throwable $th) {
                                    }
                                }
                            } elseif ($type == "Gerente Aprovação") {
                                if ($valueExp->getMudanca()->getAreaResp() != null) {
                                    if ($value == $valueExp->getMudanca()->getAreaResp()->getName()) {
                                        array_push($listMudbyArea, $valueExp->getMudanca());
                                    }
                                }
                            } elseif ($type == "Gestor da Mudança") {
                                if ($valueExp->getMudanca()->getMangerMudancas() != null) {
                                    try {
                                        //code...


                                        if ($valueExp->getMudanca()->getAddBy()->getFunction() != null) {
                                            if ($value == $valueExp->getMudanca()->getMangerMudancas()->getFunction()->getName()) {
                                                array_push($listMudbyArea, $valueExp->getMudanca());
                                            }
                                        }
                                    } catch (\Throwable $th) {
                                        //throw $th;
                                    }
                                }
                            } elseif ($type == "Área Impactada") {
                                foreach ($valueExp->getMudanca()->getAreaImpact() as $key => $areaI) {
                                    if ($areaI != null) {
                                        if (sizeof($listMudbyArea) == 0) {
                                            array_push($listMudbyArea, $valueExp->getMudanca());
                                        } else {
                                            if ($listMudbyArea[sizeof($listMudbyArea) - 1] != $valueExp->getMudanca()) {
                                                array_push($listMudbyArea, $valueExp->getMudanca());
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($valueExp->getMudanca()->getAddBy() != null) {
                                    if ($value == $valueExp->getMudanca()->getAddBy()->getFunction()->getName()) {
                                        if (sizeof($listMudbyArea) == 0) {
                                            array_push($listMudbyArea, $valueExp->getMudanca());
                                        } else {
                                            if ($listMudbyArea[sizeof($listMudbyArea) - 1] != $valueExp->getMudanca()) {
                                                
                                       
                                           
                                                array_push($listMudbyArea, $valueExp->getMudanca());
                                            }
                                        }
                                    }
                                }
                                if ($valueExp->getMudanca()->getAreaResp() != null) {
                                    if ($value == $valueExp->getMudanca()->getAreaResp()->getName()) {
                                        if (sizeof($listMudbyArea) == 0) {
                                            array_push($listMudbyArea, $valueExp->getMudanca());
                                        } else {
                                            if ($listMudbyArea[sizeof($listMudbyArea) - 1] != $valueExp->getMudanca()) {
                                                array_push($listMudbyArea, $valueExp->getMudanca());
                                            }
                                        }
                                    }
                                }
                                if ($valueExp->getMudanca()->getMangerMudancas() != null) {
                                    if ($valueExp->getMudanca()->getMangerMudancas()->getFunction() != null) {
                                        if ($value == $valueExp->getMudanca()->getMangerMudancas()->getFunction()->getName()) {
                                            if (sizeof($listMudbyArea) == 0) {
                                                array_push($listMudbyArea, $valueExp->getMudanca());
                                            } else {
                                                if ($listMudbyArea[sizeof($listMudbyArea) - 1] != $valueExp->getMudanca()) {
                                                    array_push($listMudbyArea, $valueExp->getMudanca());
                                                }
                                            }
                                        }
                                    }
                                }
                                foreach ($valueExp->getMudanca()->getAreaImpact() as $key => $areaI) {
                                    if ($areaI != null) {
                                        if ($value == $areaI->getName()) {
                                            if (sizeof($listMudbyArea) == 0) {
                                                array_push($listMudbyArea, $valueExp->getMudanca());
                                            } else {
                                                if ($listMudbyArea[sizeof($listMudbyArea) - 1] != $valueExp->getMudanca()) {
                                                    array_push($listMudbyArea, $valueExp->getMudanca());
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }
                }



                function filterList($list, $filterIds)
                {
                    $filteredList = [];
                    foreach ($list as $item) {
                        $itemId = $item->getId();

                        foreach ($filterIds as $filterId) {
                            if ($itemId === $filterId) {
                                $filteredList[] = $item;
                                break;
                            }
                        }
                    }

                    return $filteredList;
                }

                // Filter by dateInit
                if (isset($mudIds['dateInicio']) && $mudIds['dateInicio'] !== '') {
                    $list = filterList($list, $listDateInit);
                }
                // Filter by dateTermino
                if (isset($mudIds['dateTermino']) && $mudIds['dateTermino'] !== '') {
                    $list = filterList($list, $listDate);
                }
                // Filter by client
                if (isset($mudIds['client']) && $mudIds['client'] !== 'null') {
                    $list = filterList($list, $listClient);
                }
                // Filter by person
                if (isset($mudIds['person']) && $mudIds['person'] !== 'null') {
                    if ($coordinator === null) {
                        $list = filterList($list, $listDateTypePers);
                    } else {
                        $list = filterList($list, array_intersect($listDateTypePers, $listMudbyArea));
                    }
                }
                // Filter by area
                
                if (isset($mudIds['area']) && $mudIds['area'] !== 'null') {
                    $list = $listMudbyArea;
                }
                // Filter by dateApp
                if (isset($mudIds['dateApp']) && $mudIds['dateApp'] !== '' && $mudIds['dateApp'] !== 'null') {
                    $list = filterList($list, $listDateApp);
                }

                $sectors = $em->getRepository(Sector::class)->findAll();
                $client = $em->getRepository(Client::class)->findAll();
                $managers = $em->getRepository(Person::class)->findAll();
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            } else {

                $mudIds = [];
                $sectors = $em->getRepository(Sector::class)->findAll();
                $list = $em->getRepository(Mudancas::class)->findAll();
                $client = $em->getRepository(Client::class)->findAll();
                $managers = $em->getRepository(Person::class)->findAll();
                $person = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);

                foreach ($list as $key => $value) {
                    $export = $em->getRepository(ExportMud::class)->findOneBy(['mudanca' => $value->getId()]);
                    if ($export == null) {
                        $export = new ExportMud();
                        $export->setMudanca($value);
                        $process = $em->getRepository(Process::class)->findOneBy(['mudancas' => $value]);
                        $export->setProcess($process);
                        $sp = $em->getRepository(SectorProcess::class)->findBy(['process' => $process]);
                        foreach ($sp as $key => $value) {
                            $export->addSectorProcess($value);
                        }
                        $em->persist($export);
                        $em->flush();
                    }
                }
            }
            return $this->render('admin/listMudancas.html.twig', [
                'controller_name' => 'Atualizar Mudancas',
                'login' => 'null',
                'type' => 'list',
                'p' => $person,
                'client' => $client,
                'muds' => $mudIds,
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

            foreach ($mud as $value) {
                $ExportMud[] = $em->getRepository(ExportMud::class)->findOneBy(['mudanca' => $value]);
            }

            $spreadsheet = (new Excel())->generateExcel($ExportMud, $doctrine);
            $writer = new Xlsx($spreadsheet);
            $publicDirectory = $this->getParameter('kernel.project_dir');
            $excelFilepath =  $publicDirectory . '/public/Admin.xlsx';
            $writer->save($excelFilepath);
            return $this->redirectToRoute('exportFile');
        }
    }
}
