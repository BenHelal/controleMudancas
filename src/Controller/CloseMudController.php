<?php

namespace App\Controller;

use App\Entity\Departemant;
use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Sector;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CloseMudController extends AbstractController
{
    #[Route('/flow/{id}', name: 'flow')]
    public function index(ManagerRegistry $doctrine, Request $request, $id): Response
    {

        $session = new Session();
        $session = $request->getSession();

        //$request->header_remove();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);

            // condition on  situation of Mudanacas
            if ($mud == null) {
                return $this->redirectToRoute('app_mudancas');
            }
            $conn = $doctrine->getConnection();
            $sql = 'SELECT * FROM mudancas_sector  where mudancas_id = :mudancas';
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['mudancas' => $mud->getId()]);
            $dm =  $resultSet->fetchAllAssociative();
            $depOfUserAddMudancas = $em->getRepository(Departemant::class)->findBy(['name' => $person->getDepartemant()]);

            //$areaRespOfUserAddMudancas = $em->getRepository(Sector::class)->findBy(['Departemant' => $depOfUserAddMudancas]);

            foreach ($dm as $key => $vale) {

                $sql2 =
                    '   select * 
                    FROM 
                        person as p,
                        mudancas as mud , 
                        process as pr , 
                        sector_process as dp , 
                        sector as d  
                    WHERE 
                        mud.id = pr.mudancas_id and 
                        dp.process_id = pr.id and 
                        dp.person_id = p.id AND 
                        dp.sector_id = d.id and 
                        mud.id = ? ;';

                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindValue(1, $mud->getId());
                //$stmt2->bindValue(2, $dm[$key]['id']);
                $resultSet2 = $stmt2->executeQuery();
                $ln =  $resultSet2->fetchAllAssociative();
            }
            foreach ($dm as $key => $vale) {
                $sql2 =
                    '   select p.name 
                    FROM 
                        person as p,
                        mudancas as mud , 
                        process as pr , 
                        sector_process as dp , 
                        sector as d  
                    WHERE 
                        mud.id = pr.mudancas_id and 
                        dp.process_id = pr.id and 
                        dp.person_id = p.id AND 
                        dp.sector_id = d.id and 
                        mud.id = ? ;';
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindValue(1, $mud->getId());
                //$stmt2->bindValue(2, $dm[$key]['id']);
                $resultSet2 = $stmt2->executeQuery();
                $ln2 =  $resultSet2->fetchAllAssociative();
            }

            if ($ln == null) {
                $pe = null;
            } else {
                $pe = $em->getRepository(Person::class)->find($ln[0]['person_id']);
            }

            //dd($ln);


            return $this->render('close_mud/index.html.twig', [
                'controller_name' => 'CloseMudController',
                'login' => 'false',
                'person' => $person,
                'mud' => $mud,
                'p' => $pe,
                'data' => $ln,
                'data2' => $ln2
            ]);
        } else {
            return $this->redirectToRoute('log_employer');
        }
    }
}
