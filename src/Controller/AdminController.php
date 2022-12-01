<?php

namespace App\Controller;

use App\Entity\Departemant;
use App\Entity\DepartemantMudancass;
use App\Entity\Manager;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Requestper;
use App\Entity\Sector;
use App\Form\AddPersonType;
use App\Form\EditPersonType;
use App\Form\ManagerType;
use App\Form\PermissionType;
use App\Form\PersonType;
use App\Form\RequestadminType;
use App\Form\RequestperType;
use App\Form\SectorType;
use App\Model\Class\FunctionUsers;
use App\Model\Class\Sessions;
use App\Model\Class\ThemeFn;
use App\Model\Class\UsersPermission;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
            $sql = 'Delete FROM process WHERE mudancas_id = :mudancas_id ;';
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['mudancas_id' => $mudancas->getId()]);
            $ln2 =  $resultSet->fetchAllAssociative();

            $sql = 'Delete FROM mudancas_sector WHERE mudancas_id = :mudancas_id ;';
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

    #[Route('/edit/sector/{id}', name: 'edit_sector')]
    public  function sectorById($id, ManagerRegistry $doctrine, Request $request)
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
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_mudancas');
        }
    }
}
